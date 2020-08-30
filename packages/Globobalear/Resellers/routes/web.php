<?php
Route::group(['namespace' => 'Globobalear\Resellers\Controllers'], function () {
    Route::group(['middleware' => ['web', 'auth']], function () {
        
        Route::resource('resellers', 'ResellersController');
        Route::name('resellers.data')->get('data/resellers', 'ResellersController@data');

    });

  
});
