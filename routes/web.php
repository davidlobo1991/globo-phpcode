<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get(
    '/', function () {
        return redirect()->route('reservations.index');
    }
);

/**
 * Payments
 */
Route::group(['prefix' => 'web', 'namespace' => 'Web'],function () {

    Route::post('/cart', 'WebController@cartResum');
    Route::get('/cart', 'WebController@resumForm')->name('web.resum-form');

    Route::post('/payment/create', 'PaymentController@create')->name('create-pay');

    Route::get('/url-ok', 'PaymentController@urlOK')->name('payment.url-ok');
    Route::get('/url-ko', 'PaymentController@urlKO')->name('payment.url-ko');


    Route::get('/payment/execute', 'PayPalController@notification')->name('execute-pay');
    Route::get('/payment/error', 'PayPalController@error')->name('error-pay');


    Route::name('payment-notification')->post('paymentnotification','TpvController@paymentNotification');


});
/***********end payments **********/

Route::group(['middleware' => 'auth'], function () {

    Route::name('home')->get('/', 'HomeController@index');
    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

    /** INIT USERS AND ROLES ROUTES */
    Route::resource('users', 'UsersController');
    Route::name('users.data')->get('data/users', 'UsersController@data');

    Route::resource('roles', 'RolesController');
    Route::name('roles.data')->get('data/roles', 'RolesController@data');

    Route::resource('permissions', 'PermissionsController');
    Route::name('permissions.data')->get('data/permissions', 'PermissionsController@data');
    /** END USERS AND ROLES ROUTES */

    /** INIT HOMELIST */
    Route::name('reservations-home')->match(['get', 'post'],'reservations-home', 'HomeController@index');
    /** END HOMELIST */

    /** INIT BOOKING-LIST */
    Route::resource('booking', 'BookingListController');
    /** END BOOKING-LIST */

    /** INIT GLOBAL */
    Route::resource('global', 'GlobalController');
    Route::name('global.data')->get('data/global', 'GlobalController@data');
    /** END BOOKING-LIST */

    /** INIT RESERVATIONS */
    Route::resource('reservations', 'ReservationsController');

    Route::delete('reservations/{reservation}/unfinished', 'ReservationsController@destroyUnfinished')->name('reservations.destroyUnfinished');

    //Route::name('reservations.create')->get('reservations/{id}/create', 'ReservationsController@create');
    Route::name('reservations.restore')->post('reservations/{id}/restore', 'ReservationsController@restore');
    Route::name('reservations.pdf')->get('reservations/{id}/pdf', 'ReservationsController@pdf');

    Route::name('reservations.data')->get('data/reservations', 'ReservationsController@data');

    Route::name('reservations.excel')->post('/reservations/excel', 'ReservationsController@excel');

    Route::name('reservations.datacanceled')->get('datacanceled/reservations', 'ReservationsController@datacanceled');
    Route::get('canceled/', ['as' => 'reservations.canceled','uses' => 'ReservationsController@canceled']);
    Route::post('reservations/cancel', ['as' => 'reservations.cancel','uses' => 'ReservationsController@cancel']);

    Route::name('reservations.dataunfinished')->get('dataunfinished/reservations', 'ReservationsController@dataunfinished');
    Route::get('unfinished/', ['as' => 'reservations.unfinished','uses' => 'ReservationsController@unfinished']);

    Route::name('reservations.datadeleted')->get('datadeleted/reservations', 'ReservationsController@datadeleted');
    Route::get('deleted/', ['as' => 'reservations.deleted','uses' => 'ReservationsController@deleted']);

    Route::post('resendemail', ['as' => 'reservations.resendemail','uses' => 'ReservationsController@resendEmail']);
    /** END RESERVATIONS */


    /** INIT RESERVATIONS WRISTABAND*/
    //Route::get('reservation-wristbands/{id}/create', 'ReservationWristbandController@create')->name('reservationsWristbands.create');
    Route::resource('reservations-wristbands', 'ReservationWristbandController');
    Route::name('reservationsWristbands.create')->get('reservation-wristbands/{id}/create', 'ReservationWristbandController@create');
    Route::resource('reservationsWristbands', 'ReservationWristbandController');
    Route::get('reservations-wristbands/{id}/pdf', 'ReservationWristbandController@pdf')->name('reservations-wristbands-pdf');
    /** END RESERVATIONS WRISTABAND*/

    /** INIT RESERVATIONS PACK*/
    Route::name('reservationspacks.create')->get('reservationspacks/{id}/create', 'ReservationsController@createPack');
    Route::resource('reservationspacks', 'ReservationsPacksController', ['except' => ['create']]);
    Route::name('reservationspacks.create')->get('reservations-packs/{id}/create', 'ReservationsPacksController@create');
    Route::name('reservationspacks.pdf')->get('reservations-packs/{id}/pdf', 'ReservationsPacksController@pdf');
    /** END RESERVATIONS PACK*/
});
