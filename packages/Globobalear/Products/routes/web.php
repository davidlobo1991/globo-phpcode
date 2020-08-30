<?php

Route::group(['namespace' => 'Globobalear\Products\Controllers'], function () {
    Route::group(['middleware' => ['web', 'auth']], function () {
        Route::resource('products', 'ProductsController');
        Route::name('products.data')->get('data/products', 'ProductsController@data');
        Route::name('products.prices')->get('data/products/{id}/prices', 'ProductsController@prices');
        Route::name('products.checkPasses')->get('data/checkPasses', 'ProductsController@checkPasses');

        Route::resource('passes', 'PassesController');
        Route::name('passes.data')->get('data/passes', 'PassesController@data');

        Route::resource('seatTypes', 'SeatTypesController');
        Route::name('seatTypes.data')->get('data/seatTypes', 'SeatTypesController@data');

        Route::resource('ticketTypes', 'TicketTypesController');
        Route::name('ticketTypes.data')->get('data/ticketTypes', 'TicketTypesController@data');

        Route::resource('providers', 'ProvidersController');
        Route::name('providers.data')->get('data/providers', 'ProvidersController@data');
    });

    Route::post('/products/pricesSave', ['as' => 'products.pricesSave', 'uses' => 'ProductsController@pricesSave']);
    Route::post('/generateList', 'PassesController@generateList');
    Route::post('/tableProduct', 'PassesController@tableProduct');
    Route::post('/table/tickets', 'PassesController@tableTickets');
    Route::post('/list/passes', 'PassesController@list');
    Route::post('/list/products', 'ProductsController@list');
});
