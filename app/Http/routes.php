<?php

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix'=>'api','middleware'=>'auth:api'], function(){
    Route::resource('note','NoteController');
});

Route::group(['middleware'=>'web'], function (){

    Route::auth();

    Route::get('/home', 'HomeController@index');

});
