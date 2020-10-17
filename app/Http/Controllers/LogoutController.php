<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Session;

class LogoutController extends Controller
{
    /**
    * ログアウト処理
    *
    * @return Illuminate\Http\RedirectResponse
    */
    public function __invoke(){
      Session::flush();
      Auth::logout();
      return redirect('/login');
    }
}
