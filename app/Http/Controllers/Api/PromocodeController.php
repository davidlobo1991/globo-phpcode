<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Models\Web\Cart;

use App\ReservationType;

use App\Helpers\Functions;

use Log;

class PromocodeController extends Controller
{
    public function applyPromocode(Request $request)
    {
        $cart = new Cart();
        $cart->setProducts($request->products ?? []);
        $cart->setShows($request->shows ?? []);
        $cart->setPacks($request->packs ?? []);
        // $cart->setWristbandPasses($request->wristband_passes ?? []);

        $promocode = $cart->processPromocode($request->promocode ?? "");

        $data = [
            'promocode' => $promocode,
            'discount_quantity' => $cart->getDiscount(),
            'items_with_discount' => $cart->getItemsWithDiscount() ?? []
        ];

        return response()->json($data);
    }
}
