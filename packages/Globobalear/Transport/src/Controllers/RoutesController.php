<?php

namespace Globobalear\Transport\Controllers;


use App\Http\Notification\Facade\Toastr;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Transport\Models\Area;
use Globobalear\Transport\Models\PickupPoint;
use Globobalear\Transport\Models\Route;
use Yajra\Datatables\Datatables;

class RoutesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('transport::routes.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = Route::select('routes.id', 'routes.name', 'areas.name as area')
            ->join('areas', 'areas.id', '=', 'routes.area_id')
            ->get();

        return $datatables->collection($query)
            ->addColumn('action', 'transport::routes.actions')
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        $areas = Area::pluck('name', 'id');
        $pickupPoints = PickupPoint::pluck('name', 'id');

        return view('transport::routes.create')
            ->with('pickupPoints', $pickupPoints)
            ->with('areas', $areas);
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
            'name' => 'required|unique:routes,name',
        ]);

        $route = Route::create($request->all());

        $route->pickupPoints()->sync($request->pickupPoints);

        Toastr::success('Route create successfully', 'Created!');

        return redirect()->route('routes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Route $route
     * @return Redirect
     */
    public function show(Route $route)
    {
        return redirect()->route('transport::routes.edit', $route->id);
    }

    /**
     * Route the form for editing the specified resource.
     *
     * @param  Route $route
     * @return View
     */
    public function edit(Route $route)
    {
        $areas = Area::pluck('name', 'id');
        $pickupPoints = PickupPoint::pluck('name', 'id');

        return view('transport::routes.edit')
            ->with('pickupPoints', $pickupPoints)
            ->with('areas', $areas)
            ->with('route', $route);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Route $route
     * @return Redirect
     */
    public function update(Request $request, Route $route)
    {
        
        $this->validate($request, [
            'name' => 'required|unique:routes,name,' . $route->id,
         ]);
        
      
        $this->validate($request, [
           'pickupPoints.*.price' => 'required' ,
           'pickupPoints.*.hour' => 'required' ,
        ]);
        
       
        $route->update($request->all());

        $route->pickupPoints()->sync($request->pickupPoints);

        Toastr::success('Route updated successfully', 'Updated!');

        return redirect()->route('routes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Route $route
     * @return Redirect
     */
    public function destroy(Route $route)
    {
        $route->delete();

        Toastr::success('Route deleted successfully', 'Deleted!');

        return redirect()->route('routes.index');
    }


    /******************* AJAX METHODS *******************/

    /**
     * Generate ajax line for a pickup point on routes CRUD
     *
     * @param Request $request
     * @return string
     */
    public function addRoutes(Request $request)
    {
        $view = '';

        if ($request->pickupPoint) {
            try {
                $pickupPoint = PickupPoint::with('city')->find($request->pickupPoint);
            } catch (ModelNotFoundException $e) {
                return $e->getMessage();
            }

            $view = view('transport::partials.pickupPointLine', ['pickupPoint' => $pickupPoint, 'order' => $request->count]);
            $view = $view->render();

        }

        echo $view;
    }
}
