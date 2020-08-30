<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Traits\PaymentManager; //TODO  quitar
use App\Models\Web\Cart;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use PayPal\Api\Item;
use Globobalear\Wristband\Models\WristbandPass;
use Ssheduardo\Redsys\Facades\Redsys;

class WebController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cartResum(Request $request)
    {
        //test example
        //$request->data = '{"shows":[],"products":[{"realPassId":"79","seatTypeId":"1","ticketTypeId":1,"passId":"79","crsCat":"products","seatTypeName":"Generic","passDate":"2018-05-12","price":"55","quantity":"1","image":null,"showName":"Cruise","hasDiscount":false}],"packs":[],"wristbands":[],"promocode":null}';
        $data = json_decode($request->data, true);

        $cart = new Cart();
        $cart->setProducts($data['products'] ?? []);
        $cart->setShows($data['shows'] ?? []);
        $cart->setPacks($data['packs'] ?? []);
        // $cart->setWristbandPasses($data['wristband_passes'] ?? []);

        $cart->processPromocode($data['promocode'] ?? "");

        $cart->save();

        return redirect()->route('web.resum-form');
    }

    public function resumForm(Request $request)
    {
        $cart = Cart::catch();

        return view('Web.payments.index', ['cart' => $cart]);
    }
}
