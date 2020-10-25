<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use Log;
use Session;
use App\Utils\Util;

class MakeEvent extends Controller
{
  
    /**
    * イベントの作成
    * @param Request $request
    *
    * @return JSON
    */
    public function makeEvent(Request $request){

      $request->validate([
        'twitterId' => 'required|string',
        'name' => 'required|string',
        'explain' => 'nullable|string',
        'candidateDate' => 'required|string'
      ]);

      $event = new Event;

      $twitterId = $request->twitterId;
      $name = $request->name;
      $explain = $request->explain;
      $candidateDate = $request->candidateDate;
      $eventId = Util::makeEventId();

      $event->twitter_id = $twitterId;
      $event->event_id = $eventId;
      $event->name = $name;
      $event->explain = $explain;
      $event->candidate_schedule = $candidateDate;
      $event->url = '/event/'.$eventId;
      $event->save();

      return json_encode(array('eventId'=>$eventId));
    }

}
