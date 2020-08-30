<?php

Route::group(['namespace' => 'Globobalear\Payments\Controllers'], function() {
    Route::group(['middleware' => ['web', 'auth']], function () {
      
        Route::name('payments.index')->match(['get', 'post'],'payments', 'PaymentsController@index');
        Route::name('payments.getPayments')->get(config('crs.reservations-table') . '/{id}/payments', 'PaymentsController@getPayments');
        Route::name('payments.postPayments')->post(config('crs.reservations-table') . '/payments', 'PaymentsController@postPayments');
        Route::post('payments/removepayment', ['as' => 'reservations.removePayment','uses' => 'PaymentsController@removePayment']);

        Route::post('paymentxml', ['as' => 'paymentXML','uses' => 'PaymentsController@xmlPayments']);
        Route::post('paymentexcel', ['as' => 'paymentEXCEL','uses' => 'PaymentsController@excelPayments']);

    });

});

