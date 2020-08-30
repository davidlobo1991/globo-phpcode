<?php

namespace Globobalear\Transport\Controllers;


use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Transport\Models\Area;
use Yajra\Datatables\Datatables;

class AreasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('transport::areas.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = Area::select('id', 'name', 'acronym')->get();

        return $datatables->collection($query)
            ->addColumn('action', 'transport::areas.actions')
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        return view('transport::areas.create');
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
            'name' => 'required|unique:areas,name',
            'acronym' => 'required|unique:areas,acronym',
        ]);

        Area::create($request->all());

        Toastr::success('Area create successfully', 'Created!');

        return redirect()->route('areas.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Area $area
     * @return Redirect
     */
    public function show(Area $area)
    {
        return redirect()->route('transport::areas.edit', $area->id);
    }

    /**
     * Area the form for editing the specified resource.
     *
     * @param  Area $area
     * @return View
     */
    public function edit(Area $area)
    {
        return view('transport::areas.edit')
            ->with('area', $area);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Area $area
     * @return Redirect
     */
    public function update(Request $request, Area $area)
    {
        $this->validate($request, [
            'name' => 'required|unique:areas,name,' . $area->id,
            'acronym' => 'required|unique:areas,acronym,' . $area->id,
        ]);

        $area->update($request->all());

        Toastr::success('Area updated successfully', 'Updated!');

        return redirect()->route('areas.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Area $area
     * @return Redirect
     */
    public function destroy(Area $area)
    {
        $area->delete();

        Toastr::success('Area deleted successfully', 'Deleted!');

        return redirect()->route('areas.index');
    }
}
