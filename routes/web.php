<?php

route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/cabinet', 'CabinetController@index')->name('cabinet');
Route::get('/game', 'CabinetController@game')->name('game');


Route::post('/cabinet/show', 'CabinetController@show');
Route::post('/cabinet/save', 'CabinetController@save');
Route::post('/cabinet/remove', 'CabinetController@remove');
Route::post('/cabinet/avatar', 'CabinetController@avatar');

Route::post('/getFriends', 'HomeController@getFriends');
Route::post('/session/create', 'SessionController@create');
Route::post('/session/{session}/chats', 'ChatController@chats');
Route::post('/session/{session}/user', 'ChatController@userf');
Route::post('/session/{session}/read', 'ChatController@read');
Route::post('/session/{session}/clear', 'ChatController@clear');
Route::post('/session/{session}/block', 'BlockController@block');
Route::post('/session/{session}/unblock', 'BlockController@unblock');
Route::post('/send/{session}', 'ChatController@send');

Route::post('/sendToVk', 'ChatController@sendToVk');
Route::post('/cron', 'ChatController@cron');
Route::post('/addVkId', 'HomeController@addVkId');
