<?php

Route::group(['namespace' => 'Globobalear\Customers\Controllers'], function() {
    Route::group(['middleware' => ['web', 'auth']], function() {
        Route::resource('customers', 'CustomersController');
        Route::name('customers.data')->get('data/customers', 'CustomersController@data');

        Route::name('customers.reservations')->get('customers/{id}/reservations', 'CustomersController@reservations');
        
        Route::name('customers.datareservations')->get('data/{id}/datareservations', 'CustomersController@dataReservations');

        Route::resource('customers-languages', 'CustomersLanguagesController');
        Route::name('customers-languages.data')->get('data/customers-languages', 'CustomersLanguagesController@data');

        Route::resource('customers-nationalities', 'CustomersNationalitiesController');
        Route::name('customers-nationalities.data')->get('data/customers-nationalities', 'CustomersNationalitiesController@data');

       
        
    });

    Route::name('custormers.list')->post('list/customers', 'CustomersController@list');
    Route::name('custormers.get')->post('get/customers', 'CustomersController@get');
});;
