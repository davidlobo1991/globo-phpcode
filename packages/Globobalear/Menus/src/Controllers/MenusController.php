<?php

namespace Globobalear\Menus\Controllers;


use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Menus\Models\Dish;
use Globobalear\Menus\Models\Menu;
use Globobalear\Products\Models\SeatType;
use Yajra\Datatables\Datatables;

class MenusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('menus::menus.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = Menu::select('menus.id', 'menus.name','seat_types.title as seat_type')
        ->leftJoin('seat_types', 'seat_types.id', '=', 'menus.seat_type_id')
        ->get();

        return $datatables->collection($query)
            ->addColumn('action', 'menus::menus.actions')
            ->make(true);
    }

    
    /**
     * Generate ajax list for a menu on reservations CRUD
     *
     * @param Request $request
     * @return string
     */

    public function list(Request $request): JsonResponse
    {
        //dd($request->all(),'nada');
        $menu = Menu::whereIn('seat_type_id', $request->seattype )
        ->orderBy('name');
        $menu = $menu->get();
       
        return response()->json($menu);
    }

    
    /**
     * Generate ajax line for a menu on reservations CRUD
     *
     * @param Request $request
     * @return string
     */

    public function addMenu(Request $request)
    {
      
        $view = '';
       
        if ($request->menus) {
         
            try {
            $menu = Menu::with('dishes')->find($request->menus);
                
            } catch (ModelNotFoundException $e) {
                return $e->getMessage();
            }
           
            $view = view('menus::partials.menuLine', ['menu' => $menu, 'id' => $request->id]);
            $view = $view->render();
        }

        echo $view;
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        $dish = Dish::pluck('name', 'id')->toArray();
        $seatType = SeatType::pluck('title', 'id')->toArray();
        return view('menus::menus.create')
            ->with('dish', $dish)
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

        $menu = Menu::create($request->all());

        $menu->dishes()->sync($request->dishes);

        Toastr::success('Menu create successfully');

        return redirect()->route('menus.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Menu $menu
     * @return Redirect
     */
    public function show(Menu $menu)
    {
        return redirect()->route('menus::menus.edit', $menu->id);
    }

    /**
     * Pass the form for editing the specified resource.
     *
     * @param  Menu $menu
     * @return View
     */
    public function edit(Menu $menu)
    {
        $dish = Dish::pluck('name', 'id')->toArray();
        $seatType = SeatType::pluck('title', 'id')->toArray();
        return view('menus::menus.edit')
            ->with('menu', $menu)
            ->with('dish', $dish)
            ->with('seatType', $seatType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Menu $menu
     * @return Redirect
     */
    public function update(Request $request, Menu $menu)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $menu->update($request->all());

        $menu->dishes()->sync($request->dishes);

        Toastr::success('Menu updated successfully');

        return redirect()->route('menus.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Menu $menu
     * @return Redirect
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        Toastr::success(trans('menu.menus') . trans('flash.deleted') . trans('flash.successfully'), 'success');

        return redirect()->route('menus.index');
    }
}
