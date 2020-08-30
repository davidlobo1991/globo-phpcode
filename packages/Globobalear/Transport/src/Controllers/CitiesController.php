<?php

namespace Globobalear\Transport\Controllers;


use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Transport\Models\City;
use Yajra\Datatables\Datatables;

class CitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('transport::cities.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = City::select('id', 'name')->get();

        return $datatables->collection($query)
            ->addColumn('action', 'transport::cities.actions')
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        return view('transport::cities.create');
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
            'name' => 'required|unique:cities,name',
        ]);

        City::create($request->all());

        Toastr::success('City create successfully', 'Created!');

        return redirect()->route('cities.index');
    }

    /**
     * Display the specified resource.
     *
     * @param City $city
     * @return Redirect
     */
    public function show(City $city)
    {
        return redirect()->route('transport::cities.edit', $city->id);
    }

    /**
     * City the form for editing the specified resource.
     *
     * @param  City $city
     * @return View
     */
    public function edit(City $city)
    {
        return view('transport::cities.edit')
            ->with('city', $city);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  City $city
     * @return Redirect
     */
    public function update(Request $request, City $city)
    {
        $this->validate($request, [
            'name' => 'required|unique:cities,name,' . $city->id,
        ]);

        $city->update($request->all());

        Toastr::success('City updated successfully', 'Updated!');

        return redirect()->route('cities.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  City $city
     * @return Redirect
     */
    public function destroy(City $city)
    {
        $city->delete();

        Toastr::success('City deleted successfully', 'Deleted!');

        return redirect()->route('cities.index');
    }
}
