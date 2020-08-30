<?php

namespace Globobalear\Products\Controllers;

use App\GlobalConf;
use App\Helpers\Helper;
use App\Http\Notification\Facade\Toastr;
use App\Reservation;
use Globobalear\Products\Models\Pass;
use Globobalear\Products\Models\Product;
use Globobalear\Products\Models\ProductsPrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Products\Models\Category;
use Globobalear\Products\Models\SeatType;
use Globobalear\Products\Models\Provider;
use Globobalear\Products\Models\TicketType;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Auth;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (!Auth::user()->role->role_permission('view_shows')) {
            abort(403, 'Unauthorized');
        }

        return view('products::products.index', compact('langs'));
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = Product::select('products.id', 'products.name', 'products.acronym', 'products.sort as orden', 'products.commission as commis', 'categories.name as category')
            ->leftJoin('categories', 'categories.id', 'products.category_id')
            ->orderBy('sort', 'asc');

        return $datatables->eloquent($query)
            ->addColumn('action', 'products::products.actions')
            ->orderColumn('sort', 'name $1')
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        if (!Auth::user()->role->role_permission('create_shows')) {
            abort(403, 'Unauthorized');
        }

        $seatTypes = SeatType::enable()->get();
        $ticketTypes = TicketType::all();
        $categories = Category::pluck('name', 'id');
        $providers = Provider::pluck('name', 'id');

        return view('products::products.create')
            ->with('seatTypes', $seatTypes)
            ->with('categories', $categories)
            ->with('ticketTypes', $ticketTypes)
            ->with('providers', $providers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        if (!Auth::user()->role->role_permission('create_shows')) {
            abort(403, 'Unauthorized');
        }

        $this->validate($request, [
            'name' => 'required|unique:products,name',
            'acronym' => 'required|unique:products,acronym',
        ]);

        $dataInput = Helper::saveFiles($request->all());
        $dataInput = Helper::saveUncheckedCheckbox($dataInput, ['has_quota', 'has_passes']);


        $product = Product::create($dataInput);
        $product->seatTypes()->sync($request->seats);
        $product->ticketTypes()->sync($request->tickets);

        Helper::saveTranslatedFields($product, ['description'], $request);

        Toastr::success('Product create successfully');

        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return Redirect
     */
    public function product(Product $product)
    {
        if (!Auth::user()->role->role_permission('show_shows')) {
            abort(403, 'Unauthorized');
        }

        return redirect()->route('products::products.edit', $product->id);
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function checkPasses(Request $request)
    {
        if (!Auth::user()->role->role_permission('edit_shows')) {
            abort(403, 'Unauthorized');
        }

        $product = null;
        $productId = $request->get('product_id');

        if ($productId) {
            $product = Product::where('id', $productId)->first();

            if (!is_null($product) && $product->has_passes) {
                return ['status' => true];
            }

        }

        $product = Product::where('id', $productId)->with(['prices' => function($query) {
            $query->with(['seatTypes']);
        }])->first();

        $seatTypes = $product->seatTypes;
        $ticketTypes = $product->ticketTypes;
        $global = GlobalConf::first();

        if ($seatTypes->isEmpty() || $ticketTypes->isEmpty()) {
            $view = '<h4 class="text-center">This pass has not Seat or Ticket types assigned</h4>';
        } else {
            $reservation = Reservation::with('reservationTickets', 'product')->find($request->reservation);

            $view = view('products::partials.tableTicket',
                compact('seatTypes', 'ticketTypes', 'reservation','global', 'product'));

            try {
                $view = $view->render();
            } catch (\Exception $e) {
                $test = $e;
            }
        }

        return ['status' => false, 'view' => $view];
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  Product $product
     * @return View
     */
    public function edit(Product $product)
    {
        if (!Auth::user()->role->role_permission('edit_shows')) {
            abort(403, 'Unauthorized');
        }

        $seatTypes = SeatType::enable()->get();
        $ticketTypes = TicketType::all();
        $product->load('seatTypes', 'ticketTypes', 'provider');
        $categories = Category::pluck('name', 'id');
        $providers = Provider::pluck('name', 'id');

        return view('products::products.edit')
            ->with('seatTypes', $seatTypes)
            ->with('ticketTypes', $ticketTypes)
            ->with('categories', $categories)
            ->with('product', $product)
            ->with('providers', $providers);
    }

    /**
     * @param $product
     * @return mixed
     */
    public function prices($productId)
    {
        $product = Product::where('id', $productId)->first();

        $seatTypes = $product->seatTypes;
        $ticketTypes = $product->ticketTypes;
        $productsPrices = ProductsPrice::where('product_id', $productId)->get();

        return view('products::products.prices')
            ->with('product', $product)
            ->with('seatTypes', $seatTypes)
            ->with('productsPrices', $productsPrices)
            ->with('ticketTypes', $ticketTypes);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pricesSave(Request $request)
    {
      /*  if(!Auth::user()->role->role_permission('edit_shows'))
            abort(403, 'Unauthorized');*/

        foreach ($request->data as $seatType => $ticketType) {
            foreach($ticketType['ticketTypes'] as $ticket => $price) {
                $productsPrices = ProductsPrice::where('product_id', $request->product_id)
                    ->where('ticket_type_id', $ticket)
                    ->where('seat_type_id', $seatType)
                    ->first();

                if (null !== $productsPrices) {
                    $productsPrices->price = $price;
                    $productsPrices->save();
                } else {
                    if ($price) {
                        DB::table('products_prices')->insert([
                            'seat_type_id' => $seatType,
                            'ticket_type_id' => $ticket,
                            'product_id' => $request->product_id,
                            'price' => $price
                        ]);
                    }
                }
            }
        }

        Toastr::success('Product prices updated successfully');

        return redirect()->route('products.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Product $product
     * @return Redirect
     */
    public function update(Request $request, Product $product)
    {
        if (!Auth::user()->role->role_permission('edit_shows')) {
            abort(403, 'Unauthorized');
        }

        $this->validate($request, [
            'name' => 'required|unique:products,name,' . $product->id,
            'acronym' => 'required|unique:products,acronym,' . $product->id,
        ]);

        $dataInput = Helper::saveFiles($request->all());
        $dataInput = Helper::saveUncheckedCheckbox($dataInput, ['has_quota', 'has_passes']);

        $product->update($dataInput);
        $product->seatTypes()->sync($request->seats);
        $product->ticketTypes()->sync($request->tickets);

        Helper::saveTranslatedFields($product, ['description'], $request);

        Toastr::success('Product updated successfully');

        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Product $product
     * @return Redirect
     */
    public function destroy(Product $product)
    {
        if (!Auth::user()->role->role_permission('delete_shows')) {
            abort(403, 'Unauthorized');
        }

        try {
            $product->forceDelete();
            Toastr::success(trans('menu.products') . trans('flash.deleted') . trans('flash.successfully'), 'success');
            return redirect()->route('products.index');
        } catch (\Exception $e) {
            Toastr::error('You can not delete the product because it has associated pass');
            return redirect()->route('products.index');
        }
    }


    /************* AJAX METHODS *************/

    /**
     * Return list of all Products
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $products = Product::orderBy('sort', 'asc');

        $products = $products->get();

        return response()->json($products);
    }
}
