<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\Util;

class GetSessionTwitterId extends Controller
{
  /**
  * ユーザーID取得
  * @param Request $request
  *
  * @return JSON
  */
  public function __invoke(Request $request){
    $twitterId = Util::getTwitterId();
    return json_encode(array('twitterId'=>$twitterId));
  }

}
