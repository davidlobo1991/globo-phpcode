<?php

namespace App\Http\Controllers;

use App\GlobalConf;
use App\Helpers\Helper;
use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Yajra\Datatables\Datatables;
use Auth;

class GlobalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if(Auth::user()->role->role_permission('view_users')){
           
            return view('global.index');
        }else{
            abort(403);
        }
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        
        $query = GlobalConf::select('id','booking_fee','paypal')->get();
       
        return $datatables->collection($query)
            ->addColumn('action', 'global.actions')
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
       //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Response
     */
    public function store(Request $request)
    {
       //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User $user
     * @return Response
     */
    public function show(GlobalConf $global)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User $user
     * @return View
     */
    public function edit(GlobalConf $global)
    {
        if(Auth::user()->role->role_permission('edit_users')){
      
        return view('global.edit',compact('global'));
        }else{
            abort(403);
        }
    }


    /**
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, GlobalConf $global)
    {
        $this->validate($request, [
            'booking_fee' => 'required',
            'paypal' => 'required'
        ]);

       
        $global->update($request->all());


        Toastr::success('Global updated successfully');

        return redirect()->route('global.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User $user
     * @return Response
     */
    public function destroy(User $user)
    {
        //
    }
}
