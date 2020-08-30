<?php

Route::group(['namespace' => 'Globobalear\Menus\Controllers', 'middleware' => ['web', 'auth']], function () {
    Route::resource('cartes', 'CartesController');
    Route::name('cartes.data')->get('data/cartes', 'CartesController@data');

    Route::resource('menus', 'MenusController');
    Route::name('menus.data')->get('data/menus', 'MenusController@data');

    Route::resource('dishes', 'DishesController');
    Route::name('dishes.data')->get('data/dishes', 'DishesController@data');

    Route::post('/list/menus', 'MenusController@list');
    Route::post('/addmenu/reservations', 'MenusController@addMenu');
});
