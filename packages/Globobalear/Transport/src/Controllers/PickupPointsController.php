<?php

namespace Globobalear\Transport\Controllers;


use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Transport\Models\City;
use Globobalear\Transport\Models\PickupPoint;
use Yajra\Datatables\Datatables;

class PickupPointsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('transport::pickupPoints.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = PickupPoint::select('pickup_points.id', 'pickup_points.name', 'latitude', 'longitude', 'cities.name as city')
            ->join('cities', 'cities.id', '=', 'pickup_points.city_id')
            ->get();

        return $datatables->collection($query)
            ->addColumn('action', 'transport::pickupPoints.actions')
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        $cities = City::pluck('name', 'id');

        return view('transport::pickupPoints.create')
            ->with('cities', $cities);
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
            'name' => 'required|unique:pickup_points,name',
        ]);

        PickupPoint::create($request->all());

        Toastr::success('Pickup Point create successfully', 'Created!');

        return redirect()->route('pickup-points.index');
    }

    /**
     * Display the specified resource.
     *
     * @param PickupPoint $pickupPoint
     * @return Redirect
     */
    public function show(PickupPoint $pickupPoint)
    {
        return redirect()->route('transport::pickupPoints.edit', $pickupPoint->id);
    }

    /**
     * PickupPoint the form for editing the specified resource.
     *
     * @param  PickupPoint $pickupPoint
     * @return View
     */
    public function edit(PickupPoint $pickupPoint)
    {
        $cities = City::pluck('name', 'id');

        return view('transport::pickupPoints.edit')
            ->with('cities', $cities)
            ->with('pickupPoint', $pickupPoint);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  PickupPoint $pickupPoint
     * @return Redirect
     */
    public function update(Request $request, PickupPoint $pickupPoint)
    {
        $this->validate($request, [
            'name' => 'required|unique:pickup_points,name,' . $pickupPoint->id,
        ]);

        $pickupPoint->update($request->all());

        Toastr::success('Pickup Point updated successfully', 'Updated!');

        return redirect()->route('pickup-points.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  PickupPoint $pickupPoint
     * @return Redirect
     */
    public function destroy(PickupPoint $pickupPoint)
    {
        $pickupPoint->delete();

        Toastr::success('Pickup Point deleted successfully', 'Deleted!');

        return redirect()->route('pickup-points.index');
    }
}
