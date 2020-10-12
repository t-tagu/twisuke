<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Utils\Util;

class HeaderNavigationController extends Controller
{
  /**
  * ユーザーID取得
  *
  * @return Collection
  */
  public function getId(Request $request){

    $twitterId = Util::getTwitterId();
    return json_encode(array('twitterId'=>$twitterId));

  }

}
