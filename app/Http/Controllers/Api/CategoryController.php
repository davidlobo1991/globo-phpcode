<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Globobalear\Products\Models\Category;
use Globobalear\Products\Models\Product;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all(['id', 'name', 'acronym']);

        return response()->json(['data' => $categories]);
    }

    /**
     * @param $lang
     * @param $category_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function productsByCategory($lang, $category_id)
    {
        $products = Product::where('category_id', $category_id)->get(['id', 'name as title', 'description']);

        return response()->json(['data' => $products]);
    }

}
