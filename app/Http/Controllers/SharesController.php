<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\User;
use Log;
use Session;
use App\Utils\Util;

class SharesController extends Controller
{
  /**
  * DMの宛先(相互フォローの情報）を取得
  *
  * @return Collection
  */
  public function getFollower(){

      $twitterId = Util::getTwitterId();

      $accessToken = '';
      $accessTokenSecret = '';

      $tokens = User::where('twitter_id',$twitterId)->get(['access_token','access_token_secret']);

      foreach($tokens as $data){
        $accessToken = $data->access_token;
        $accessTokenSecret = $data->access_token_secret;
      }

      $connection = new TwitterOAuth(config('apikey.api_key'), config('apikey.secret_key'), $accessToken, $accessTokenSecret);
      $connection->setTimeouts(60,60);

      $followerIds = []; //フォロワーのIDを格納
      $followerParams = array('user_id'=>$twitterId,'count'=>5000,'cursor'=>-1,'stringify_ids'=>true); //followers/ids APIのパラメータ

      do { //フォロワーのtwitterIdを取得する
           $res = $connection->get('followers/ids',$followerParams);
           $resCode = $connection->getLastHttpCode(); //レスポンスコードを取得
           if($resCode === config('twitter.response.Success')){
             foreach($res->ids as $id){
                $followerIds[] = $id; //IDを追加していく
             }
           }else{
             Util::handleTwitterApiError($resCode, $connection);
             return;
           }
        }while($followerParams['cursor'] = $res->next_cursor_str); //cursorにnext_cursorを指定して繰り返す

      $followIds = []; //フォローのIDを格納
      $friendsParams = array('user_id'=>$twitterId,'count'=>5000,'cursor'=>-1,'stringify_ids'=>true); //followers/ids APIのパラメータ

      do { //フォロワーのtwitterIdを取得する
           $res = $connection->get('followers/ids',$friendsParams);
           $resCode = $connection->getLastHttpCode(); //レスポンスコードを取得
           if($resCode === config('twitter.response.Success')){ //通信成功時
             foreach($res->ids as $id){
               $followIds[] = $id; //IDを追加していく
             }
           }else{
             Util::handleTwitterApiError($resCode, $connection);
             return;
           }
         }while($friendsParams['cursor'] = $res->next_cursor_str); //cursorにnext_cursorを指定して繰り返す

      $mutualFollowerIds = array_intersect($followerIds, $followIds); //相互フォローのIDを格納

      $splitedData = array_chunk($mutualFollowerIds, 100); //相互フォローIDリストを最大100件ごとの配列に分ける(API制限のため)

      $profileArray = array(); //DM送信先の情報を格納(相互フォローのみ)

      for($i = 0; $i < count($splitedData); $i++){ //ツイッターIDを最大100件ずつ処理

        $profiles = $connection->get('users/lookup', array('user_id' => implode(',', $splitedData[$i]))); //ツイッターユーザーのデータをまとめて取得
        $profilesResCode = $connection->getLastHttpCode();

        if($profilesResCode === config('twitter.response.Success')){ //通信成功時
          foreach($profiles as $value){ //ユーザーのデータ(最大100件)を1つずつ展開
            $name = $value->name;
            $screenName = $value->screen_name;
            $image = $value->profile_image_url;
            $id = $value->id_str;
            $array = array('name'=>$name, 'screenName'=>$screenName, 'image'=> $image, 'twitterId'=> $id);
            array_push($profileArray, $array);
          }
          return $profileArray;
        }else{
          Util::handleTwitterApiError($resCode, $connection);
          return;
        }
      }
  }

  /**
  * DMを送信する
  *
  *
  */
  public function sendMessage(Request $request){

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
        $messa = $value->name."さん"."\n".$message;
        $id = $value->id_str;
        $array = array('id'=> $id, 'message'=>$message);
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
