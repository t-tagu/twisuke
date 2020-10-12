<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Session;
use Log;

class HandleTwitterDataController extends Controller
{
    //

    /**
    * ユーザー情報の登録、更新
    *
    * @return json
    */
    public function makeUserData(Request $request){

      $request->validate([
        'twitterIdStr' => 'required|string',
        'accountName' => 'required|string',
        'displayName' => 'required|string',
        'photoURL' => 'required|string',
        'accessToken' => 'required|string',
        'accessTokenSecret' => 'required|string'
      ]);

      $user = User::where('twitter_id',$request->twitterIdStr)->first();

      if(!$user){
        //ユーザーが存在しない場合はデータを保存
        User::create(['twitter_id' => $request->twitterIdStr, 'account_name'=>$request->accountName, 'display_name'=>$request->displayName,
                      'photo_url'=>$request->photoURL, 'access_token'=>$request->accessToken,'access_token_secret'=>$request->accessTokenSecret]);
      }else{
        //データ更新
        User::where('twitter_id',$request->twitterIdStr)
             ->update(['account_name'=>$request->accountName, 'display_name'=>$request->displayName,'photo_url'=>$request->photoURL,
                       'access_token'=>$request->accessToken,'access_token_secret'=>$request->accessTokenSecret]);
      }

      //セッションに情報にユーザーID、トークンを保存
      Session::regenerate();
      Session::put('twitter_id',$request->twitterIdStr);
      Session::put('access_token',$request->accessToken);
      Session::put('access_token_secret',$request->accessTokenSecret);

      $user = User::where('twitter_id',$request->twitterIdStr)->first();
      Auth::login($user); //認証させる

      return json_encode(array('twitterId'=>$request->twitterIdStr,'accountName'=>$request->accountName,'displayName'=>$request->displayName,
                               'photoURL'=>$request->photoURL,'accessToken'=>$request->accessToken,'accessTokenSecret'=>$request->accessTokenSecret));

    }

}
