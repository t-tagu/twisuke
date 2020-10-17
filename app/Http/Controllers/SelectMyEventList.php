<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;

class SelectMyEventList extends Controller
{
  /**
  * 作成したイベント一覧の取得
  *
  * @param Request $request
  * @return Collection
  */
  public function selectMyEventList(Request $request){

    $request->validate([
      'twitterId' => 'required|string'
    ]);

    $eventData = Event::where('twitter_id',$request->twitterId)->get(['name','event_id']);
    return $eventData;
  }

}
