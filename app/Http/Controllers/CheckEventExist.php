<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;

class CheckEventExist extends Controller
{
  /**
  * イベントの有無を確認
  *
  * @return JSON
  */
  public function checkEventExist(Request $request){

    $request->validate([
      'eventId' => 'required|string',
    ]);

    $eventId = $request->eventId;
    $eventExists = Event::where('event_id',$eventId)->exists();

    return json_encode(array('isEventExist'=>$eventExists));
  }
}
