<?php

namespace Globobalear\Menus\Controllers;


use App\Helpers\Helper;
use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Menus\Models\CarteMenu;
use Globobalear\Menus\Models\Dish;
use Globobalear\Menus\Models\DishesType;
use Globobalear\Menus\Models\MenuDish;
use Yajra\Datatables\Datatables;

class DishesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('menus::dishes.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        
        
        //$disher = Dish::with('dishesType')->get();
       // dd($disher);
        
        $query = Dish::select('dishes.id', 'dishes.name', 'dishes.vegetarian','dishes_types.name as type')
        ->leftJoin('dishes_types', 'dishes_types.id', '=', 'dishes.dishes_type_id')
        ->get();


     
        return $datatables->collection($query)
            ->addColumn('action', 'menus::dishes.actions')
            ->editColumn('vegetarian', function ($q) {
                return $q->vegetarian ? 'Yes' : 'No';
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
        $dishesType = DishesType::pluck('name', 'id');

        return view('menus::dishes.create')
            ->with('dishesType', $dishesType);
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
            'name' => 'required',
            'dishes_type_id' => 'required'
        ]);

        $dataInput = Helper::saveUncheckedCheckbox($request->all(), ['vegetarian']);

        Dish::create($dataInput);

        Toastr::success('Dish create successfully');

        return redirect()->route('dishes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Dish $dish
     * @return Redirect
     */
    public function show(Dish $dish)
    {
        return redirect()->route('menus::dishes.edit', $dish->id);
    }

    /**
     * Pass the form for editing the specified resource.
     *
     * @param  Dish $dish
     * @return View
     */
    public function edit(Dish $dish)
    {
        $dishesType = DishesType::pluck('name', 'id')->toArray();

        return view('menus::dishes.edit')
            ->with('dishesType', $dishesType)
            ->with('dish', $dish);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Dish $dish
     * @return Redirect
     */
    public function update(Request $request, Dish $dish)
    {
        $this->validate($request, [
            'name' => 'required',
            'dishes_type_id' => 'required'
        ]);

        $dataInput = Helper::saveUncheckedCheckbox($request->all(), ['vegetarian']);

        $dish->update($dataInput);

        Toastr::success('Dish updated successfully');

        return redirect()->route('dishes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Dish $dish
     * @return Redirect
     */
    public function destroy(Dish $dish)
    {
        $dish->delete();

        Toastr::success(trans('menu.dishes') . trans('flash.deleted') . trans('flash.successfully'), 'success');

        return redirect()->route('dishes.index');
    }
}
