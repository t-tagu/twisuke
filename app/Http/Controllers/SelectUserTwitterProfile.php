<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Utils\Util;
use Log;

class SelectUserTwitterProfile extends Controller
{
    /**
    * ユーザーアカウントデータの取得
    *
    * @return JSON
    */
    public function __invoke(){

      $twitterId = Util::getTwitterId();

      $accountName = '';
      $displayName = '';
      $photoUrl = '';

      $accountData = User::where('twitter_id',$twitterId)
                         ->get(['account_name','display_name','photo_url']);

      foreach($accountData as $data){
        $accountName = $data->account_name;
        $displayName = $data->display_name;
        $photoUrl = $data->photo_url;
      }

      return json_encode(array('accountName'=>$accountName,'displayName'=>$displayName,
                               'photoUrl'=>$photoUrl, 'twitterId'=>$twitterId));
    }
}
