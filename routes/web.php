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

//ツイッターアカウントデータの登録
Route::post('/make_user_data', 'HandleTwitterDataController@makeUserData');
//認証チェック
Route::get('/auth_check', 'AuthCheckController@authCheck');

//ヘッダーナビゲーションの処理
Route::get('/get_id', 'HeaderNavigationController@getId');

//サイドバーの処理
Route::get('/get_account_data', 'SidebarController@getAccountData');

//イベント作成ページでの処理
Route::post('/make_event', 'ScheduleController@makeEvent');

//日程入力ページでの処理
Route::post('/get_event_data', 'EventsController@getEventData');
Route::post('/enter_schedule', 'EventsController@enterSchedule');
Route::post('/check_event_exist', 'EventsController@checkEventExist');

//モーダルでの処理
Route::get('/get_follower', 'SharesController@getFollower');
Route::post('/send_message', 'SharesController@sendMessage');

//作成イベント一覧での処理
Route::post('/get_my_event_data', 'MyEventsController@getMyEventData');
Route::post('/get_event_detail', 'MyEventsController@getEventDetail');
Route::post('/check_user_exist', 'MyEventsController@checkUserExist');

//ログアウト
Route::get('/logout', 'LogoutController@getLogout');

Route::get('/{any}', function () {
      return view('index');
})->where('any', '.*');
