<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//ツイッターアカウントデータの登録または更新してログイン
Route::post('/sign_up', 'SignUp@singUp');
//イベント作成
Route::post('/make_event', 'MakeEvent@makeEvent');
//スケジュールの投票
Route::post('/vote_schedule', 'VoteSchedule@voteSchedule');
//イベントの存在チェック
Route::post('/check_event_exist', 'CheckEventExist@checkEventExist');
//ユーザーの相互フォロワーを取得
Route::post('/get_user_twitter_mutual_followers', 'GetUserTwitterMutualFollowers@getUserTwitterMutualFollowers');
//ツイッターのDMを送信
Route::post('/send_direct_messages', 'SendDirectMessages@sendDirectMessages');
//作成したイベント一覧の取得
Route::post('/select_my_event_list', 'SelectMyEventList@selectMyEventList');
//選択したイベントの詳細情報の取得
Route::post('/select_my_event_detail', 'SelectMyEventDetail@selectMyEventDetail');
//ユーザーが存在しているかチェック
Route::post('/check_user_exist', 'CheckUserExist@checkUserExist');

Route::get('/{any}', function () {
      return view('index');
})->where('any', '.*');
