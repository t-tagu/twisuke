<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Session;
use Log;

class SignUp extends Controller
{

  /**
  * ユーザー情報の登録
  *
  * @param Request $request
  */
    public function singUp(Request $request)
    {
      $request->validate([
        'twitterIdStr' => 'required|string',
        'accessToken' => 'required|string',
        'accessTokenSecret' => 'required|string'
      ]);

      $user = User::where('twitter_id',$request->twitterIdStr)->first();

      if(!$user){
        //ユーザーが存在しない場合はデータを保存
        User::create(['twitter_id' => $request->twitterIdStr,'access_token'=>$request->accessToken,'access_token_secret'=>$request->accessTokenSecret]);
      }

    }
}
