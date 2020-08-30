<?php

namespace Globobalear\Wristband\Controllers;

use App\Http\Notification\Facade\Toastr;
use App\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Globobalear\Wristband\Requests\WristbandPassRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Resellers\Models\Reseller;
use Globobalear\Products\Models\Product;
use Globobalear\Products\Models\ShowPirates;
use Globobalear\Wristband\Models\Wristband;
use Globobalear\Wristband\Models\WristbandPass;
use Yajra\Datatables\Datatables;
use Auth;

class WristbandPassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if(! Auth::user()->role->role_permission('view_wristband_passes')){
            return abort(403);
        }

        return view('wristband::wristband-pass.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = WristbandPass::select('wristband_passes.id', 'wristband_passes.title','date_start', 'date_end', 'price', 'quantity',
        DB::raw('wristbands.title as wristband_id'),
        DB::raw('CONCAT(wristband_passes.title , "| between (" , date_format(date_start, "%d/%m/%Y"), " | " , date_format(date_end, "%d/%m/%Y"),") " )as name')
        )
       
        ->leftJoin('wristbands', 'wristbands.id', 'wristband_passes.wristband_id')
        ->orderBy('title','asc');

        return $datatables->eloquent($query)
            ->addColumn('action', 'wristband::wristband-pass.actions')
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
        if(! Auth::user()->role->role_permission('create_wristband_passes')){
            return abort(403);
        }

        $wristband = Wristband::pluck('title', 'id');
        $products = Product::all();
        $showPirates = ShowPirates::all();

        return view('wristband::wristband-pass.create', compact('wristband', 'products', 'showPirates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Redirect
     */
    public function store(WristbandPassRequest $request)
    {
        if(! Auth::user()->role->role_permission('create_wristband_passes')){
            return abort(403);
        }

        $rbPass = WristbandPass::create($request->all());
        $rbPass->products()->attach($request->products_globo_balear);
        $rbPass->showsPirates()->attach($request->shows_pirates);

        Toastr::success('WristbandPass create successfully', 'Created!');

        return redirect()->route('wristband-pass.index');
    }


    /**
     * @param Reseller $reseller
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Wristband $wristband)
    {
        if(! Auth::user()->role->role_permission('show_wristband_passes')){
            return abort(403);
        }

        return redirect()->route('wristband::wristband-pass.edit', $wristband->id);
    }

    /**
     * @param Wristband $wristband
     * @return $this
     */
    public function edit(WristbandPass $wristbandPass)
    {
        if(! Auth::user()->role->role_permission('edit_wristband_passes')){
            return abort(403);
        }

        $wristband = Wristband::pluck('title', 'id');
        $products = Product::all();
        $showPirates = ShowPirates::all();

        return view('wristband::wristband-pass.edit', compact('wristbandPass', 'wristband', 'products', 'showPirates'));
    }

    /**
     * @param Request $request
     * @param Wristband $wristband
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(WristbandPassRequest $request, WristbandPass $wristbandPass)
    {
        if(! Auth::user()->role->role_permission('edit_wristband_passes')){
            return abort(403);
        }

        $wristbandPass->update($request->all());
        $wristbandPass->products()->sync($request->products_globo_balear);
        $wristbandPass->showsPirates()->sync($request->shows_pirates);

        Toastr::success('Wristband pass updated successfully', 'Updated!');

        return redirect()->route('wristband-pass.index');
    }

    /**
     * @param Wristband $wristband
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(WristbandPass $wristbandPass)
    {
        if(! Auth::user()->role->role_permission('delete_wristband_passes')){
            return abort(403);
        }

        $wristbandPass->delete();

        Toastr::success('Wristband pass deleted successfully', 'Deleted!');

        return redirect()->route('wristband-pass.index');
    }


    /************* AJAX METHODS *************/

    /**
     * Return list of all Passes by Show
     *
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $passes = WristbandPass::where('wristband_id', $request->q)->orderBy('date_start');

        if (isset($request->s) && !is_null($request->s)) {
            $passes->where('date_start', 'like', "%{$request->s}%");
        }

        return response()->json($passes->get());
    }


    public function formSelectComponent(Request $request)
    {
        $wristbands = Wristband::orderBy('title','asc')->pluck('title', 'id');
        $numElemento = $request->element;
        $reservation = Reservation::find($request->reservation);

        if($reservation){
            return  view('wristband::components.formSelectComponentEdit', compact('wristbands', 'numElemento', 'reservation'))->render();
        }

        return  view('wristband::components.formSelectComponent', compact('wristbands', 'numElemento', 'reservation'))->render();
    }

    public function productsHtml(Request $request)
    {
        $wristbandPass = WristbandPass::find($request->pass_id);

        return  view('wristband::components.products', compact('wristbandPass'))->render();
    }
}
