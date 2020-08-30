<?php

namespace Globobalear\Wristband\Controllers;

use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Resellers\Models\Reseller;
use Globobalear\Wristband\Models\Wristband;
use Yajra\Datatables\Datatables;
use Auth;

class WristbandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if(! Auth::user()->role->role_permission('view_wristbands')){
            return abort(403);
        }

        return view('wristband::wristband.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = Wristband::select( 'id', 'title', 'acronym')->orderBy('title','asc');

        return $datatables->eloquent($query)
            ->addColumn('action', 'wristband::wristband.actions')
            ->orderColumn('title', 'title $1')
            ->make(true);
    }
    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        if(! Auth::user()->role->role_permission('create_wristbands')){
            return abort(403);
        }

        return view('wristband::wristband.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        if(! Auth::user()->role->role_permission('create_wristbands')){
            return abort(403);
        }

        $this->validate($request, [
            'title' => 'required',
            'acronym' => 'required'
        ]);

        Wristband::create($request->all());

        Toastr::success('Wristband create successfully', 'Created!');

        return redirect()->route('wristband.index');
    }


    /**
     * @param Reseller $reseller
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Wristband $wristband)
    {
        if(! Auth::user()->role->role_permission('show_wristbands')){
            return abort(403);
        }

        return redirect()->route('wristband::wristband.edit', $wristband->id);
    }

    /**
     * @param Wristband $wristband
     * @return $this
     */
    public function edit(Wristband $wristband)
    {
        if(! Auth::user()->role->role_permission('edit_wristbands')){
            return abort(403);
        }

        return view('wristband::wristband.edit')->with('wristband', $wristband);
    }

    /**
     * @param Request $request
     * @param Wristband $wristband
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Wristband $wristband)
    {
        if(! Auth::user()->role->role_permission('edit_wristbands')){
            return abort(403);
        }

        $this->validate($request, [
            'title' => 'required',
            'acronym' => 'required'
        ]);

        $wristband->update($request->all());

        Toastr::success('Wristband updated successfully', 'Updated!');

        return redirect()->route('wristband.index');
    }

    /**
     * @param Wristband $wristband
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Wristband $wristband)
    {
        if(! Auth::user()->role->role_permission('delete_wristbands')){
            return abort(403);
        }

        $wristband->delete();

        Toastr::success('Wristband deleted successfully', 'Deleted!');

        return redirect()->route('wristband.index');
    }


    /************* AJAX METHODS *************/

    /**
     * Return list of all Shows
     *
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        $wristbands = Wristband::orderBy('title','asc')->get(['title as name', 'id']);

        return response()->json($wristbands);
    }

}
