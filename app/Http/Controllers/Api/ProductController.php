<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Traits\PassFormat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Globobalear\Products\Models\Product;

class ProductController extends Controller
{
    use PassFormat;
    /**
     * @return Product
     */
    public function index()
    {
        $products = Product::with('category')->get();

        return response()->json(['data' => $products]);
    }

    /**
     * @param $lang
     * @param $id
     * @return array
     */
    public function show($lang, $id)
    {
        $product = Product::where('id', $id)
            ->with([
                'prices.seatTypes',
                'seatTypes',
                'ticketTypes'
            ])
            ->get(['id', 'name as title', 'description', 'has_passes'])->first();

        return response()->json(['data' => $product]);
    }

    /**
     * @param $lang
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function passesByProduct($lang, $id)
    {
        $product = Product::where('id', $id)->getNextPasses()->first();
        $data = $this->setFormat($product->passes ?? []);

        return response()->json(['data' => $data]);
    }


    /**
     * @param $lang
     * @param $id
     */
    /*public function wristbands($lang, $id)
    {
        $product = Product::find($id);
        $wristbandPasses = $product->wristbandPasses;

        $wristbands = [];
        foreach($wristbandPasses as $key => $wrPass){
            $wristbands[] = $wrPass->wristband;
        }
        $wristbands = array_unique($wristbands, SORT_REGULAR);

        return response()->json(['data' => $wristbands]);
    }*/
}
