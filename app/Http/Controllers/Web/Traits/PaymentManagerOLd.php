<?php
/**
 * Created by PhpStorm.
 * User: moussa
 * Date: 30/11/17
 * Time: 9:52
 */
namespace App\Http\Controllers\Web\Traits;

use App\Models\Web\Cart;
use Illuminate\Support\Collection;
use PayPal\Api\Item;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Globobalear\Products\Models\Pass;
use Globobalear\Wristband\Models\WristbandPass;


trait PaymentManager
{
    /**
     * @return ApiContext
     */
    private function getApiContext(): ApiContext
    {
        $clientId = env('PAYPAL_ID');
        $clientSecret = env('PAYPAL_PASS');
        $mode = 'sandbox';

        $apiContext = new ApiContext(
            new OAuthTokenCredential($clientId, $clientSecret)
        );

        $apiContext->setConfig([
            'mode' => $mode,
            'log.LogEnabled' => env('PAYPAL_LOG', false),
            //TODO CAMBIAR RUTA paypal.log !!!
            'log.FileName' => $_SERVER['DOCUMENT_ROOT'] ."/paypal.log",
            'log.LogLevel' => env('PAYPAL_LOG_LEVEL', 'FINE'),
            'validation.level' => 'log',
            'cache.enabled' => false,
        ]);

        return $apiContext;
    }
}