<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class AuthCheckController extends Controller
{
  /**
  * 認証とセッションに保存したツイッターIDの取得のチェック
  *
  * @return JSON
  */
  public function authCheck(){

    $auth = false;
    if(Auth::check() && (!empty(Session::get('twitter_id'))) ){
      $auth = true;
    }
    return json_encode(array('authStatus'=>$auth));
  }
}
