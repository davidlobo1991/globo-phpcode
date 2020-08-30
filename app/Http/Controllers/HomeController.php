<?php

/*
 * Taken from
 * https://github.com/laravel/framework/blob/5.3/src/Illuminate/Auth/Console/stubs/make/controllers/HomeController.stub
 */

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\Http\Notification\Facade\Toastr;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\GlobalConf;
use App\PassesSeller;
use Globobalear\Products\Models\Product;
use Globobalear\Products\Models\Pass;
use Globobalear\Products\Models\SeatType;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $viewType = isset($request->viewType) ? $request->viewType : 'availability';
        $currentDate = Carbon::now();
        $passes = Pass::whereNull('canceled_at')->orderBy('datetime', 'asc');

        //filtro por shows
        if (isset($request->products) && $request->products > 0) {
            $passes = $passes->where('product_id', $request->products);
        }

        //filtro por fecha de inicio  
        if (isset($request->dateStart)) {
            $dateStart = Carbon::createFromFormat('d-m-Y', $request->dateStart)->startOfDay();
            $startDate = $dateStart->format('d-m-Y');
        } else {
            $dateStart = $currentDate->startOfDay();
            $startDate = $currentDate->format('d-m-Y');
        }

        //filtro por fecha de fin
        if (isset($request->dateEnd)) {
            $dateEnd = Carbon::createFromFormat('d-m-Y', $request->dateEnd)->endOfDay();
            $passes = $passes->where('datetime', '<=', $dateEnd->format('Y-m-d H:i:s'));

        }

        $global = GlobalConf::first();
        $products = Product::pluck('name', 'id')->prepend('-', 0);
        $passesSeller = PassesSeller::orderBy('id', 'asc')->get();
        $passes = $passes->where('datetime', '>=', $dateStart->format('Y-m-d H:i:s'));
        $passes = $passes->paginate(10);

        $SeatType = SeatType::orderBy('sort')->where('is_enable', 1)->get();

        return view('home-list.index')
            ->with('viewType', $viewType)
            ->with('request', $request)
            ->with('startDate', $startDate)
            ->with('global', $global)
            ->with('products', $products)
            ->with('passesSeller', $passesSeller)
            ->with('passes', $passes)
            ->with('seatType', $SeatType);
    }
}