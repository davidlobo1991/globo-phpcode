<?php

namespace Globobalear\Menus\Controllers;


use App\Helpers\Helper;
use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Menus\Models\Carte;
use Globobalear\Menus\Models\CarteMenu;
use Globobalear\Menus\Models\Menu;
use Globobalear\Products\Models\Product;
use Globobalear\Products\Models\SeatType;
use Yajra\Datatables\Datatables;

class CartesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('menus::cartes.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = Carte::select('cartes.id', 'cartes.name', 'cartes.is_enable','products.name as product','seat_types.title as seat_type')
        ->leftJoin('products', 'products.id', '=', 'cartes.product_id')
        ->leftJoin('seat_types', 'seat_types.id', '=', 'cartes.seat_type_id')
        ->get();

        return $datatables->collection($query)
            ->addColumn('action', 'menus::cartes.actions')
            ->setRowClass(function ($q) {
                return !$q->is_enable ? 'canceled' : '';
            })
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        $menus = Menu::pluck('name', 'id')->toArray();

        $products = Product::pluck('name', 'id')->toArray();

        $seatType = SeatType::pluck('title', 'id')->toArray();
       
        return view('menus::cartes.create')
            ->with('menus', $menus)
            ->with('products', $products)
            ->with('seatType', $seatType);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $dataInput = Helper::saveUncheckedCheckbox($request->all(), ['is_enable']);
         
        $carte = Carte::create($dataInput);

        

        $carte->menus()->sync($request->menus);

        Toastr::success('Carte create successfully');

        return redirect()->route('cartes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Carte $carte
     * @return Redirect
     */
    public function show(Carte $carte)
    {
        return redirect()->route('menus::cartes.edit', $carte->id);
    }

    /**
     * Pass the form for editing the specified resource.
     *
     * @param  Carte $carte
     * @return View
     */
    public function edit(Carte $carte)
    {
        $menus = Menu::pluck('name', 'id')->toArray();
        $products = Product::pluck('name', 'id')->toArray();
        $seatType = SeatType::pluck('title', 'id')->toArray();
        $carte->load('menus');
       
      
        return view('menus::cartes.edit')
            ->with('menus', $menus)
            ->with('carte', $carte)
            ->with('products', $products)
            ->with('seatType', $seatType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Carte $carte
     * @return Redirect
     */
    public function update(Request $request, Carte $carte)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

       

        $dataInput = Helper::saveUncheckedCheckbox($request->all(), ['is_enable']);

        $carte->update($dataInput);

        $carte->menus()->sync($request->menus);

        Toastr::success('Carte updated successfully');

        return redirect()->route('cartes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Carte $carte
     * @return Redirect
     */
    public function destroy(Carte $carte)
    {
        $carte->delete();

        Toastr::success(trans('menu.user') . trans('flash.deleted') . trans('flash.successfully'), 'success');

        return redirect()->route('cartes.index');
    }
}
