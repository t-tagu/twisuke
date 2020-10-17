<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class CheckUserExist extends Controller
{
  /**
  * ユーザーの存在チェック
  *
  * @param Request $request
  * @return JSON
  */
  public function checkUserExist(Request $request){

    $request->validate([
      'twitterId' => 'required|string',
    ]);

    $isUserExist = User::where('twitter_id',$request->twitterId)->exists();
    return json_encode(array('isUserExist'=>$isUserExist));
  }
}
