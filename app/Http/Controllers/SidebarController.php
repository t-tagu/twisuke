<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Utils\Util;

use Session;
use Log;

class SidebarController extends Controller
{
    /**
    * アカウントデータの取得
    *
    * @return Collection
    */
    public function getAccountData(){

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
