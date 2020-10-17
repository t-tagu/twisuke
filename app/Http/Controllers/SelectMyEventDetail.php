<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Event;
use App\Vote;
use App\User;
use Session;
use Log;

class SelectMyEventDetail extends Controller
{
    const MARU = "○";
    const SANKAKU = "△";
    const BATSU = "×";
    const VOTE_OF_MARU = 1;
    const VOTE_OF_SANKAKU = 2;
    const FROM_EVENT_PAGE = 1;
    const FROM_MY_EVENT_PAGE = 2;

    /**
    * イベントの詳細情報を取得
    *
    * @param Request $request
    * @return JSON|void
    */
    public function selectMyEventDetail(Request $request){

      $request->validate([
        'eventId' => 'required|string',
        'transitionFrom' => 'required|integer'
      ]);

      $eventData = Event::where('event_id',$request->eventId)->get(['twitter_id','name','explain','candidate_schedule']);
      $twitterId = '';
      $eventName = '';
      $explain = '';
      $candidateDate = '';

      foreach($eventData as $data){
        $twitterId = $data->twitter_id;
        $eventName = $data->name;
        $explain = $data->explain;
        $candidateDate = json_decode($data->candidate_schedule);
      }

      //候補日程への投票数をカウントする配列を作成
      $voteCount = array_fill(0, count($candidateDate), array_fill(0, 3, 0));

      $voteData = Vote::where('event_id',$request->eventId)->get(['twitter_id','vote','comment']);
      $twitterIds = array();
      $voteDate = array();
      $comment = array();

      foreach($voteData as $data){
        array_push($twitterIds, $data->twitter_id);
        array_push($voteDate, json_decode($data->vote));
        if(!empty($data->comment)){ //コメントを取得
          array_push($comment, $data->comment);
        }else{
          array_push($comment, '');
        }
      }

      //候補日の長さの配列で、その中に人数分の長さの配列を格納する(2次元配列)
      $personalChoice = array_fill(0, count($candidateDate), array_fill(0, count($twitterIds), "×"));

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
          $attendance = self::BATSU;
          if($voteDate[$i][$m] === self::VOTE_OF_MARU){ //○の場合
            $attendance = self::MARU;
          }elseif($voteDate[$i][$m] === self::VOTE_OF_SANKAKU){ //△の場合
            $attendance = self::SANKAKU;
          }
          $personalChoice[$m][$i] = $attendance; //逆にする
        }
      }

      for($i = 0; $i < count($voteDate); $i++){ //$i→$i+1人目、$m→$m+1日目の候補日
        for($m = 0; $m < count($voteDate[$i]); $m++){
          if($voteDate[$i][$m] === self::VOTE_OF_MARU){ //○の場合
            $voteCount[$m][0] += 1;
          }elseif($voteDate[$i][$m] === self::VOTE_OF_SANKAKU){ //△の場合
            $voteCount[$m][1] += 1;
          }else{ //×の場合
            $voteCount[$m][2] += 1;
          }
        }
      }

      switch ($request->transitionFrom) {
        case self::FROM_EVENT_PAGE:
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
          break;
        case self::FROM_MY_EVENT_PAGE:
          return json_encode(array('explain'=>$explain,'candidateDate'=>$candidateDate,'vote'=>$voteCount,
                                 'voters'=>$twitterName,'comments'=>$comment,'attendance'=>$personalChoice));
          break;
        default:
          abort(422,'パラメータエラーが発生しました。');
          break;
      }

    }
}
