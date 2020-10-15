<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Event;
use App\Vote;
use App\User;
use Session;
use Log;

class MyEventsController extends Controller
{
    const MARU = "○";
    const SANKAKU = "△";
    const BATSU = "×";
    const VOTE_OF_MARU = 1;
    const VOTE_OF_SANKAKU = 2;

    /**
    * ユーザーの存在チェック
    *
    * @return json
    */
    public function checkUserExist(Request $request){

      $request->validate([
        'twitterId' => 'required|string',
      ]);

      $isUserExist = User::where('twitter_id',$request->twitterId)->exists();

      return json_encode(array('isUserExist'=>$isUserExist));
    }


    /**
    * 作成したイベント一覧の取得
    *
    * @return Collection
    */
    public function getMyEventData(Request $request){

      $request->validate([
        'twitterId' => 'required|string'
      ]);

      $eventData = Event::where('twitter_id',$request->twitterId)->get(['name','event_id']);

      return $eventData;
    }

    /**
    * イベントの詳細情報を取得
    *
    * @return Collection
    */
    public function getEventDetail(Request $request){

      $request->validate([
        'eventId' => 'required|string'
      ]);

      $eventData = Event::where('event_id',$request->eventId)->get(['explain','candidate_schedule']);
      $explain = '';
      $candidateDate = '';

      foreach($eventData as $data){
        $explain = $data->explain;
        $candidateDate = json_decode($data->candidate_schedule);
      }

      //候補日程への投票数をカウントする配列を作成
      $voteCount = array_fill(0, count($candidateDate), array_fill(0, 3, 0));

      $voteData = Vote::where('event_id',$request->eventId)->get(['twitter_id','vote','comment']);
      $twitterId = array();
      $voteDate = array();
      $comment = array();

      foreach($voteData as $data){
        array_push($twitterId, $data->twitter_id);
        //array_push($voteDate, explode(",", $data->vote));
        array_push($voteDate, json_decode($data->vote));
        if(!empty($data->comment)){ //コメントを取得
          array_push($comment, $data->comment);
        }else{
          array_push($comment, '');
        }
      }

      //候補日の長さの配列で、その中に人数分の長さの配列を格納する(2次元配列)
      $personalChoice = array_fill(0, count($candidateDate), array_fill(0, count($twitterId), "×"));

      $twitterName = array();
      if(!empty($twitterId)){
        $connection = new TwitterOAuth(config('apikey.api_key'),config('apikey.secret_key'));
        $connection->setTimeouts(60, 60);
        $res = $connection->get('users/lookup', array("user_id" => implode(',',$twitterId)) ); //同じidのデータは1つにまとめて送られてくる
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

      return json_encode(array('explain'=>$explain,'candidateDate'=>$candidateDate,'vote'=>$voteCount,
                               'voters'=>$twitterName,'comments'=>$comment,'attendance'=>$personalChoice));
    }
}
