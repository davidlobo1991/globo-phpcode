<?php
Route::group(['namespace' => 'Globobalear\Packs\Controllers'], function () {
    Route::group(['middleware' => ['web', 'auth']], function () {

        Route::resource('packs', 'PacksController');
        Route::name('packs.data')->get('data/packs', 'PacksController@data');
        Route::post('/generatelistpack', 'PacksController@generateList')->name('packs::packs.generateList');
        Route::post('/generatelistproductspack', 'PacksController@generateShowsList')->name('packs::packs.generate-list-shows-pack');
        Route::post('/list/seatTypes', 'PacksController@listSeatTypes')->name('packs::packs.list-seat-types');
        Route::post('/list/showSeatTypes', 'PacksController@listShowSeatTypes')->name('packs::packs.list-show-seat-types');
        Route::post('/table/packs', 'PacksController@tablePacks');
        Route::post('/table/packs', 'PacksController@tablePacks');
    });
});
