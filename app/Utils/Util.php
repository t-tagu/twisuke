<?php

 namespace app\Utils;

 use Session;
 use Illuminate\Support\Facades\Auth;

 class Util { //共通処理クラス

   /**
   * ツイッターIDを取得、取得失敗ならログアウトする
   *
   * @return String|void
   */
   public static function getTwitterId(){
     if(!empty(Session::get('twitter_id'))){
       return Session::get('twitter_id');
     }else{
       Session::flush();
       Auth::logout();
       abort(401,'セッション情報の取得に失敗しました。ログイン画面に遷移します。');
       return;
     }
   }

   /**
   * ランダム文字列(イベントID)を作成
   * @param int $length
   *
   * @return String
   */
   public static function makeEventId($length = 32){
     return array_reduce(range(1, $length), function($p){
       return $p.str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz')[0];
     });
   }

   /**
   * Twitter APIエラー時の処理
   * @param int $responseCode
   * @param TwitterOAuth $connection
   *
   * @return void
   */
   public static function handleTwitterApiError($responseCode, $connection){
     $defaultErrorCode = 500; //vue側ではサーバーエラーとして受け取る
     switch($responseCode){
        case config('twitter.response.NotModified'):
          abort($defaultErrorCode,'データが取得できませんでした。');
          break;
        case config('twitter.response.BadRequest'):
          abort($defaultErrorCode,'無効なリクエストです。');
          break;
        case config('twitter.response.Unauthorized'):
          $defaultErrorCode = 401;
          abort($defaultErrorCode,'認証エラーが発生しました。');
          break;
        case config('twitter.response.NotFound'):
          abort($defaultErrorCode,'リクエストが無効です。');
          break;
        case config('twitter.response.Gone'):
          abort($defaultErrorCode,'接続しようとしているTwitterAPIが停止しています。');
          break;
        case config('twitter.response.Forbidden'):
          $errorCode = $connection->getLastBody()->errors[0]->code;
          if(!empty($errorCode) && ($errorCode === config('error.code.Suspended') || $errorCode === config('error.code.TemporarilyLocked'))){ //アカウント凍結時のエラーか判定
            abort($defaultErrorCode,'アカウントが凍結されています。凍結解除後に再試行して下さい。');
          }else{
            abort($defaultErrorCode,'Twitterによりアクセスが拒否されました。'); //その他の403エラー
          }
          break;
        case config('twitter.response.TooManyRequests'):
          abort($defaultErrorCode,'TwitterAPIの使用に一時的に制限がかかっています。しばらく時間を空けて再試行して下さい。');
          break;
        case config('twitter.response.InternalServerError'):
          abort($defaultErrorCode,'サーバーエラーが発生しました。');
          break;
        case config('twitter.response.BadGateway'):
          abort($defaultErrorCode,'Twitterサーバーがダウンしています。');
          break;
        case config('twitter.response.ServiceUnavailable'):
          abort($defaultErrorCode,'エラーが発生しました。少々時間を空けて再試行して下さい。');
          break;
        case config('twitter.response.GatewayTimeout'):
          abort($defaultErrorCode,'エラーが発生しました。少々時間を空けて再試行して下さい。');
          break;
      }
   }


 }
