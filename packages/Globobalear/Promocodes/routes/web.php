<?php

Route::group(['namespace' => 'Globobalear\Promocodes\Controllers'], function() {
    Route::group(['middleware' => ['web', 'auth']], function () {
        Route::resource('promocodes', 'PromocodesController');
        Route::name('promocodes.data')->get('data/promocodes', 'PromocodesController@data');
    });

    Route::post('/try-promocode', 'PromocodesController@tryPromocode')->name('check-promocode');
});

