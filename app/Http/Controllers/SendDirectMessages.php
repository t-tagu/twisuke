<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\User;
use App\Utils\Util;
use Log;
use Session;

class SendDirectMessages extends Controller
{

  /**
  * DMを送信する
  *
  * @param Request $request
  * @return void
  */
  public function sendDirectMessages(Request $request){

    $request->validate([
      'message' => 'required|string',
      'destination' => 'required|string'
    ]);

    $message = $request->message;

    $twitterId = Util::getTwitterId();

    $accessToken = '';
    $accessTokenSecret = '';

    $tokens = User::where('twitter_id',$twitterId)->get(['access_token','access_token_secret']);

    foreach($tokens as $data){
      $accessToken = $data->access_token;
      $accessTokenSecret = $data->access_token_secret;
    }

    $destinationInfo = array();

    $connection = new TwitterOAuth(config('apikey.api_key'), config('apikey.secret_key'), $accessToken, $accessTokenSecret);
    $connection->setTimeouts(60,60); //タイムアウトの設定

    $res = $connection->get('users/lookup', array('user_id' => $request->destination)); //ツイッターユーザーのデータをまとめて取得
    $resCode = $connection->getLastHttpCode(); //レスポンスコードを取得

    if($resCode === config('twitter.response.Success')){
      foreach($res as $value){
        $messageWithName = $value->name."さん"."\n".$message; //名前付きメッセージ
        $id = $value->id_str;
        $array = array('id'=> $id, 'message'=>$messageWithName);
        array_push($destinationInfo, $array);
      }
    }else{
      Util::handleTwitterApiError($resCode, $connection);
      return;
    }

    for($i = 0; $i < count($destinationInfo); $i++){
      $data = array('event' => [
                     'type' => 'message_create',  // 固定値 (必須)
                     'message_create' => [
                     'target' => [
                       'recipient_id' => $destinationInfo[$i]['id']  // 送信先ユーザーID (必須)
                      ],
                      'message_data' => [
                        'text' => $destinationInfo[$i]['message']
                      ]
                    ]
                 ]);
      $res = $connection->post('direct_messages/events/new', $data, true);
      $resCode = $connection->getLastHttpCode(); //レスポンスコードを取得
      if($resCode === config('twitter.response.Success')){
        sleep(3); //連投になりすぎないように3秒ほど間隔を空ける
      }else{
        Util::handleTwitterApiError($resCode, $connection);
        return;
      }
    }

  }

}
