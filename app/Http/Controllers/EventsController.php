<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Event;
use App\User;
use App\Vote;
use Log;
use Session;
use Input;
use App\Utils\Util;

class EventsController extends Controller
{

  /**
   * 出欠入力ページの表示
   *
   * @return \Illuminate\Contracts\Support\Renderable
  */
  public function index(){
    return view('event');
  }

  /**
  * イベントデータ取得
  *
  * @return json
  */
  public function getEventData(Request $request){

    $request->validate([
      'eventId' => 'required|string',
    ]);

    $eventId = $request->eventId;

    $eventExists = Event::where('event_id',$eventId)->exists();

    $eventData = Event::where('event_id',$eventId)->get(['name','explain','candidate_schedule']);
    $eventName = '';
    $explain = '';
    $candidateDate = '';

    foreach($eventData as $data){
      $eventName = $data->name;
      $explain = $data->explain;
      $candidateDate = $data->candidate_schedule;
    }

    //候補日程への投票数をカウントする配列を作成
    $voteCount = array_fill(0, count(explode("\n", $candidateDate)), array_fill(0, 3, 0));

    $voteData = Vote::where('event_id',$eventId)->get(['twitter_id','vote','comment']);
    $twitterIds = array(); //投票者のtwitter_id
    $voteDate = array();
    $comment = array();

    foreach($voteData as $data){
      array_push($twitterIds, $data->twitter_id);
      array_push($voteDate, explode(",", $data->vote));
      if(!empty($data->comment)){ //コメントを取得
        array_push($comment, $data->comment);
      }else{
        array_push($comment, '');
      }
    }

    //候補日の長さの配列で、その中に人数分の長さの配列を格納する(2次元配列)
    $personalChoice = array_fill(0, count(explode("\n", $candidateDate)), array_fill(0, count($twitterIds), "×"));

    $twitterName = array();
    if(!empty($twitterIds)){
      $connection = new TwitterOAuth(config('apikey.api_key'),config('apikey.secret_key'));
      $connection->setTimeouts(60, 60);
      $res = $connection->get('users/lookup', array("user_id" => implode(',',$twitterIds)) ); //同じidのデータは1つにまとめて送られてくる
      $resCode = $connection->getLastHttpCode(); //レスポンスコードを取得
      if($resCode === config('twitter.response.Success')){
        foreach($res as $data){ //投票者名を取得
          array_push($twitterName, $data->name);
        }
      }else{
        Util::handleTwitterApiError($resCode, $connection);
        return;
      }
    }

    for($i = 0; $i < count($voteDate); $i++){ //$i→$i+1人目、$m→$m+1日目の候補日
      for($m = 0; $m < count($voteDate[$i]); $m++){
        $attendance = "×";
        if($voteDate[$i][$m] === '1'){ //○の場合
          $attendance = "○";
        }elseif($voteDate[$i][$m] === '2'){ //△の場合
          $attendance = "△";
        }
        $personalChoice[$m][$i] = $attendance; //逆にする
      }
    }

    for($i = 0; $i < count($voteDate); $i++){
      for($m = 0; $m < count($voteDate[$i]); $m++){
        if($voteDate[$i][$m] === '1'){
          $voteCount[$m][0] += 1;
        }elseif($voteDate[$i][$m] === '2'){
          $voteCount[$m][1] += 1;
        }else{
          $voteCount[$m][2] += 1;
        }
      }
    }

    //Log::info(print_r($voteCount, true));

    $twitterId = Util::getTwitterId(); //イベント作成者のtwitter_id

    $accessToken = '';
    $accessTokenSecret = '';

    $tokens = User::where('twitter_id',$twitterId)->get(['access_token','access_token_secret']);

    foreach($tokens as $data){
      $accessToken = $data->access_token;
      $accessTokenSecret = $data->access_token_secret;
    }

    $connection = new TwitterOAuth(config('apikey.api_key'),config('apikey.secret_key'), $accessToken, $accessTokenSecret);
    $connection->setTimeouts(60,60);
    $res = $connection->get('users/show', array('user_id'=>$twitterId));
    $resCode = $connection->getLastHttpCode();
    if($resCode === config('twitter.response.Success')){
      $secretaryName = $res->name;
      $secretaryAccount = $res->screen_name;
      $profileImage = $res->profile_image_url;

      return json_encode(array('eventName'=>$eventName,'explain'=>$explain,'candidateDate'=>$candidateDate,
                               'secretaryName'=>$secretaryName,'secretaryAccount'=>$secretaryAccount,
                               'profileImage'=>$profileImage,'vote'=>$voteCount,
                                'voters'=>$twitterName,'comments'=>$comment,'attendance'=>$personalChoice));
    }else{
      Util::handleTwitterApiError($resCode, $connection);
      return;
    }

  }

  /**
  * スケジュール入力
  */
  public function enterSchedule(Request $request){

    $request->validate([
      'eventId' => 'required|string',
      'submissionDate' => 'required|string',
      'comment' => 'nullable|string'
    ]);

    $twitterId = Util::getTwitterId();

    $eventId = $request->eventId;
    $submissionDate = $request->submissionDate;
    $comment = $request->comment;

    //すでに投票していないか確認
    $isDataExist = Vote::where('twitter_id',$twitterId)->where('event_id',$eventId)->exists();

    if($isDataExist){ //投票がある場合は日程、コメントをアップデート
      Vote::where('twitter_id',$twitterId)
          ->where('event_id',$eventId)
          ->update(['vote' => $submissionDate,'comment' => $comment]);
    }else{
      $event = new Vote;
      $event->event_id = $eventId;
      $event->twitter_id = $twitterId;
      $event->vote = $submissionDate;
      $event->comment = $comment;
      $event->save();
    }

  }

  /**
  * イベントの有無を確認
  *
  * @return json
  */
  public function checkEventExist(Request $request){

    $request->validate([
      'eventId' => 'required|string',
    ]);

    $eventId = $request->eventId;
    $eventExists = Event::where('event_id',$eventId)->exists();

    return json_encode(array('isEventExist'=>$eventExists));
  }

}
