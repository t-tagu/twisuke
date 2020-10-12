<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class AuthCheckController extends Controller
{
  public function authCheck(){

    $auth = false;
    if(Auth::check() && (!empty(Session::get('twitter_id'))) ){ //認証とセッションの取得のチェック
      $auth = true;
    }
    return json_encode(array('authStatus'=>$auth));
  }
}
