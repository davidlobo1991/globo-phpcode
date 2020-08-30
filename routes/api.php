<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1','middleware' => 'auth:api'], function () {
    //    Route::resource('task', 'TasksController');

    //Please do not remove this if you want adminlte:route and adminlte:link commands to works correctly.
    #adminlte_api_routes
});

Route::group(['prefix' => 'v2/{lang}', 'middleware' => 'setLocaleMiddleware', 'namespace' => 'Api'],function () {

    /**
     * Shows
     */
    Route::get('products', 'ProductController@index');
    Route::get('products/checkPasses/{id}', 'ProductController@checkPasses');
    Route::get('products/{id}', 'ProductController@show');
    Route::get('products/{id}/passes', 'ProductController@passesByProduct');
    Route::get('/passes/product/light/{show_id}', 'ProductController@passesByProduct');
    // Route::get('products/{id}/wristbands', 'ProductController@wristbands');   //wristbands asociadas a cada bar(show)

    /**
     * Category
     */
    Route::get('categories', 'CategoryController@index');
    Route::get('categories/{category_id}/products', 'CategoryController@productsByCategory');

    /**
     * Wristbands
     */
/*    Route::get('wristbands', 'WristbandController@index');
    Route::get('wristbands/{id}', 'WristbandController@show');
    Route::get('wristbands/{id}/passes', 'WristbandController@passes');
    Route::get('wristband-passes', 'WristbandPassController@index');
    Route::get('wristband-passes/{id}', 'WristbandPassController@show');
    Route::get('wristband-pass/{id}', 'WristbandPassController@pass');*/

    /**
     * Passes and WristbandPasses
     */
    Route::get('passes', 'PassController@index');
    Route::get('passes/{id}', 'PassController@show');
    Route::get('passes/date/{month?}/{year?}', 'PassController@passesByDate');
    Route::get('passes/product/{product_id?}/{take?}', 'PassController@passesByProduct');
    Route::get('passes/product/dateRange/{date1?}/{date2?}/{product_id?}', 'PassController@passesByProductAndDateRange');


    //Availability

    Route::get('passavailability/{id}', 'PassController@availability');
    Route::get('wristbandavailability/{id}', 'WristbandPassController@availability');

    /**
     * SeatTypes
     */
    Route::get('/seattypes', 'SeatTypeController@index');

    /**
     * Packs
     */
    Route::get('packs', 'PackController@index');
    Route::get('packs/{id}', 'PackController@show');

    /**
     * Promocodes
     */
    Route::post('/apply-promocode', 'PromocodeController@applyPromocode');
});
