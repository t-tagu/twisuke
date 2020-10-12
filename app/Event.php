<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $guarded = [
      'id','created_at','updated_at'
  ];

  /**
   * イベントに属する投票を取得
   */
  public function votes()
  {
      return $this->hasMany('App\Vote','event_id');
  }
}
