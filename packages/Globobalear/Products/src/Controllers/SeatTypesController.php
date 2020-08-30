<?php

namespace Globobalear\Products\Controllers;


use App\Helpers\Helper;
use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Products\Models\SeatType;
use Yajra\Datatables\Datatables;
use Auth;

class SeatTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if(!Auth::user()->role->role_permission('show_seatypes'))
            abort(403, 'Unauthorized');

        return view('products::seatTypes.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = SeatType::select('id', 'title','default_quantity', 'acronym','sort','is_enable')->orderBy('sort','asc');

        return $datatables->eloquent($query)
        ->editColumn('is_enable', function ($q) {
            return $q->is_enable ? 'Yes' : 'No';
        })
        ->setRowClass(function ($q) {
            return !$q->is_enable ? 'canceled' : '';
        })
        ->addColumn('action', 'products::seatTypes.actions')
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
        if(!Auth::user()->role->role_permission('create_seatypes'))
            abort(403, 'Unauthorized');

        return view('products::seatTypes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        if(!Auth::user()->role->role_permission('create_seatypes'))
            abort(403, 'Unauthorized');

        $this->validate($request, [
            'title' => 'required|unique:seat_types,title',
            'acronym' => 'required|unique:seat_types,acronym',
        ]);

        $seatType = SeatType::create($request->all());

        Helper::saveTranslatedFields($seatType, ['description'], $request);

        Toastr::success('Seat Type create successfully', 'Created!');

        return redirect()->route('seatTypes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param SeatType $seatType
     * @return Redirect
     */
    public function show(SeatType $seatType)
    {
        if(!Auth::user()->role->role_permission('show_seatypes'))
            abort(403, 'Unauthorized');

        return redirect()->route('products::seatTypes.edit', $seatType->id);
    }

    /**
     * SeatType the form for editing the specified resource.
     *
     * @param  SeatType $seatType
     * @return View
     */
    public function edit(SeatType $seatType)
    {
        if(!Auth::user()->role->role_permission('edit_seatypes'))
            abort(403, 'Unauthorized');

        return view('products::seatTypes.edit')
            ->with('seatType', $seatType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  SeatType $seatType
     * @return Redirect
     */
    public function update(Request $request, SeatType $seatType)
    {
        if(!Auth::user()->role->role_permission('edit_seatypes'))
            abort(403, 'Unauthorized');

        $this->validate($request, [
            'title' => 'required|unique:seat_types,title,' . $seatType->id,
            'acronym' => 'required|unique:seat_types,acronym,' . $seatType->id,
        ]);

        $seatType->update($request->all());

        Helper::saveTranslatedFields($seatType, ['description'], $request);

        Toastr::success('Seat Type updated successfully', 'Updated!');

        return redirect()->route('seatTypes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  SeatType $seatType
     * @return Redirect
     */
    public function destroy(SeatType $seatType)
    {
        if(!Auth::user()->role->role_permission('delete_seatypes'))
            abort(403, 'Unauthorized');


        $seatType->is_enable = !$seatType->is_enable;

        $seatType->save();

        if (!$seatType->is_enable) {
            Toastr::success('Seat Type canceled successfully', 'Canceled!');
        } else {
            Toastr::success('Seat Type activated successfully', 'Activated!');
        }
        return redirect()->route('seatTypes.index');
    }

}
