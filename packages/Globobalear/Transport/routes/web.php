<?php

Route::group(['namespace' => 'Globobalear\Transport\Controllers'], function () {
    Route::group(['middleware' => ['web', 'auth']], function () {
        Route::resource('transporters', 'TransportersController');
        Route::name('transporters.data')->get('data/transporters', 'TransportersController@data');

        Route::resource('buses', 'BusesController');
        Route::name('buses.data')->get('data/buses', 'BusesController@data');

        Route::resource('areas', 'AreasController');
        Route::name('areas.data')->get('data/areas', 'AreasController@data');

        Route::resource('pickup-points', 'PickupPointsController');
        Route::name('pickup-points.data')->get('data/pickupPoints', 'PickupPointsController@data');

        Route::resource('cities', 'CitiesController');
        Route::name('cities.data')->get('data/cities', 'CitiesController@data');

        Route::resource('routes', 'RoutesController');
        Route::name('routes.data')->get('data/routes', 'RoutesController@data');
    });

    Route::post('/addpoint/routes', 'RoutesController@addRoutes');

    Route::post('/list/pickupPoints', 'BusesController@listPickupPoints');
    Route::post('/list/buses', 'BusesController@list');
    Route::post('/addtransport/reservations', 'BusesController@addTransport');

   

});

