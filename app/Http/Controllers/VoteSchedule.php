<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utils\Util;
use App\Vote;

class VoteSchedule extends Controller
{
  /**
  * スケジュール入力の値を受け取り保存または上書き保存
  *
  * @param Request $request
  * @return void
  */
  public function voteSchedule(Request $request){

    $request->validate([
      'eventId' => 'required|string',
      'submissionDate' => 'required|json',
      'comment' => 'nullable|string'
    ]);

    $twitterId = Util::getTwitterId();

    $eventId = $request->eventId;
    $submissionDate = $request->submissionDate;
    $comment = $request->comment;

    //すでに投票していないか確認
    $isDataExist = Vote::where('twitter_id',$twitterId)->where('event_id',$eventId)->exists();

    if($isDataExist){ //投票がある場合は日程、コメントをアップデート
      Vote::where('twitter_id',$twitterId)
          ->where('event_id',$eventId)
          ->update(['vote' => $submissionDate,'comment' => $comment]);
    }else{
      $event = new Vote;
      $event->event_id = $eventId;
      $event->twitter_id = $twitterId;
      $event->vote = $submissionDate;
      $event->comment = $comment;
      $event->save();
    }

  }

}
