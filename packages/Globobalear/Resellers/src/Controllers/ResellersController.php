<?php

namespace Globobalear\Resellers\Controllers;

use App\Helpers\Helper;
use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Resellers\Models\Reseller;
use Globobalear\Resellers\Models\ResellersType;
use Globobalear\Resellers\Models\AgentTypes;
use Globobalear\Transport\Models\Area;
use Globobalear\Resellers\Models\Countries;
use App\Language;
use App\User;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Auth;

class ResellersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if(!Auth::user()->role->role_permission('show_resellers'))
            abort(403, 'Unauthorized');

        return view('resellers::resellers.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
     public function data(Datatables $datatables)
     {
         if(!Auth::user()->role->role_permission('show_resellers'))
             abort(403, 'Unauthorized');

         $query = Reseller::select('resellers.id as id','resellers.name', 'resellers.company as company', 'email', 'phone','is_enable','resellers_types.name as resellertype','agent_types.name as agenttype','areas.acronym')
         ->leftJoin('resellers_types', 'resellers_types.id', '=', 'resellers.resellers_type_id')
         ->leftJoin('agent_types', 'agent_types.id', '=', 'resellers.agent_type_id')
         ->leftJoin('areas', 'areas.id', '=', 'resellers.area_id')
         ->get();
         return $datatables->collection($query)
             ->addColumn('action', 'resellers::resellers.actions')
             ->make(true);
     }
    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        if(!Auth::user()->role->role_permission('create_resellers'))
            abort(403, 'Unauthorized');

        $resellerType = ResellersType::pluck('name', 'id')->toArray();
        $area = [''=>'-'] + Area::pluck('name', 'id')->toArray();
        $agenttypes = [''=>'-'] + AgentTypes::pluck('name', 'id')->toArray();
        $user = [''=>'-'] + User::pluck('name', 'id')->toArray();
        $countries = [''=>'-'] + Countries::pluck('name', 'id')->toArray();
        $language = Language::pluck('name', 'id')->toArray();
        return view('resellers::resellers.create')
        ->with('resellerType', $resellerType)
        ->with('area', $area)
        ->with('agenttypes', $agenttypes)
        ->with('user', $user)
        ->with('countries', $countries)
        ->with('language', $language);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        if(!Auth::user()->role->role_permission('create_resellers'))
            abort(403, 'Unauthorized');

        $this->validate($request, [
            'name' => 'required',
            'discount' => 'required',
            'company' => 'required',
            'email' => 'required|email|unique:resellers,email',
        ]);

        $request->merge(['passes_seller_id' => $request->resellers_type_id]);
        
        $reseller = Reseller::create($request->all());

        Toastr::success('Reseller create successfully', 'Created!');

        return redirect()->route('resellers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param SeatType $seatType
     * @return Redirect
     */
    public function show(Reseller $reseller)
    {
        if(!Auth::user()->role->role_permission('show_resellers'))
            abort(403, 'Unauthorized');

        //return redirect()->route('shows::seatTypes.edit', $seatType->id);
    }

    /**
     * SeatType the form for editing the specified resource.
     *
     * @param  SeatType $seatType
     * @return View
     */
    public function edit(Reseller $reseller)
    {
        if(!Auth::user()->role->role_permission('edit_resellers'))
            abort(403, 'Unauthorized');

        $resellerType = ResellersType::pluck('name', 'id')->toArray();
        $area = [''=>'-'] + Area::pluck('name', 'id')->toArray();
        $agenttypes = [''=>'-'] + AgentTypes::pluck('name', 'id')->toArray();
        $user = [''=>'-'] + User::pluck('name', 'id')->toArray();
        $countries = [''=>'-'] + Countries::pluck('name', 'id')->toArray();
        $language = Language::pluck('name', 'id')->toArray();
        return view('resellers::resellers.edit')
            ->with('resellers', $reseller)
            ->with('resellerType', $resellerType)
            ->with('area', $area)
            ->with('agenttypes', $agenttypes)
            ->with('user', $user)
            ->with('countries', $countries)
            ->with('language', $language);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  SeatType $seatType
     * @return Redirect
     */
    public function update(Request $request, Reseller $reseller)
    {
        if(!Auth::user()->role->role_permission('edit_resellers'))
            abort(403, 'Unauthorized');

        $this->validate($request, [
            'name' => 'required',
            'discount' => 'required',
            'company' => 'required',
            'email' => 'required|email|unique:resellers,email,' . $reseller->id,
        ]);
        
        $request->merge(['passes_seller_id' => $reseller->resellers_type_id]);

        $reseller->update($request->all());

        Toastr::success('Reseller updated successfully', 'Updated!');

        return redirect()->route('resellers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  SeatType $seatType
     * @return Redirect
     */
    public function destroy(Reseller $reseller)
    {
        if(!Auth::user()->role->role_permission('delete_resellers'))
            abort(403, 'Unauthorized');

        $reseller->delete();

        Toastr::success('Reseller deleted successfully', 'Deleted!');

        return redirect()->route('resellers.index');
    }
    
}
