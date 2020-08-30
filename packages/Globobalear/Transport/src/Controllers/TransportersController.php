<?php

namespace Globobalear\Transport\Controllers;


use App\Helpers\Helper;
use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Transport\Models\Transporter;
use Yajra\Datatables\Datatables;

class TransportersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('transport::transporters.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = Transporter::select('id', 'name')->get();

        return $datatables->collection($query)
            ->addColumn('action', 'transport::transporters.actions')
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        return view('transport::transporters.create');
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
            'name' => 'required|unique:transporters,name',
        ]);

        Transporter::create($request->all());

        Toastr::success('Transporter create successfully', 'Created!');

        return redirect()->route('transporters.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Transporter $transporter
     * @return Redirect
     */
    public function show(Transporter $transporter)
    {
        return redirect()->route('transport::transporters.edit', $transporter->id);
    }

    /**
     * Transporter the form for editing the specified resource.
     *
     * @param  Transporter $transporter
     * @return View
     */
    public function edit(Transporter $transporter)
    {
        return view('transport::transporters.edit')
            ->with('transporter', $transporter);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Transporter $transporter
     * @return Redirect
     */
    public function update(Request $request, Transporter $transporter)
    {
        
       
        $this->validate($request, [
          'name' => 'required|unique:transporters,name,' . $transporter->id,
        ]);
                
        $transporter->update($request->all());

        Toastr::success('Transporter updated successfully', 'Updated!');

        return redirect()->route('transporters.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Transporter $transporter
     * @return Redirect
     */
    public function destroy(Transporter $transporter)
    {
        $transporter->delete();

        Toastr::success('Transporter deleted successfully', 'Deleted!');

        return redirect()->route('transporters.index');
    }
}
