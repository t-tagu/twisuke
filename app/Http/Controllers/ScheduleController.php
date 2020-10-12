<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use Log;
use Session;
use App\Utils\Util;

class ScheduleController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * イベントの作成
    *
    * @return Collection
    */
    public function makeEvent(Request $request){

      $request->validate([
        'name' => 'required|string',
        'explain' => 'nullable|string',
        'candidateDate' => 'required|string'
      ]);

      $event = new Event;

      $twitterId = Util::getTwitterId();
      $name = $request->name;
      $explain = $request->explain;
      $candidateDate = $request->candidateDate;
      $eventId = self::makeEventId();

      $event->twitter_id = $twitterId;
      $event->event_id = $eventId;
      $event->name = $name;
      $event->explain = $explain;
      $event->candidate_schedule = $candidateDate;
      $event->url = '/event/'.$eventId;
      $event->save();

      return json_encode(array('eventId'=>$eventId));
    }

    /**
    * ランダム文字列(イベントID)を作成
    *
    * @return String
    */
    static function makeEventId($length = 32){
      return array_reduce(range(1, $length), function($p){
        return $p.str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz')[0];
      });
    }
}
