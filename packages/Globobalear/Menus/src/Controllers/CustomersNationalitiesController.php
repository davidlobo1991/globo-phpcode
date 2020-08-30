<?php

namespace Globobalear\Customers\Controllers;


use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Customers\Models\CustomersNationality;
use Yajra\Datatables\Datatables;

class CustomersNationalitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('customers::customersnationalities.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = CustomersNationality::select('id','name')->get();

        $result = $datatables->collection($query)
            ->addColumn('action', 'customers::customersnationalities.actions')
            ->make(true);

        return $result;
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        return view('customers::customersnationalities.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:customers_languages,name',
        ]);

        CustomersNationality::create($request->all());

        Toastr::success('Customer language create successfully');

        return redirect()->route('customers-nationalities.index');
    }

    /**
     * Display the specified resource.
     *
     * @param CustomersNationality $customersNationality
     * @return Redirect
     */
    public function show(CustomersNationality $customersNationality)
    {
        return redirect()->route('customers::customersnationalities.edit', $customersNationality->id);
    }

    /**
     * Pass the form for editing the specified resource.
     *
     * @param  CustomersNationality $customersNationality
     * @return View
     */
    public function edit(CustomersNationality $customersNationality)
    {
        return view('customers::customersnationalities.edit')
            ->with('customersNationality', $customersNationality);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  CustomersNationality $customersNationality
     * @return Redirect
     */
    public function update(Request $request, CustomersNationality $customersNationality)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $customersNationality->update($request->all());

        Toastr::success('Customer updated successfully');

        return redirect()->route('customers-nationalities.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  CustomersNationality $customersNationality
     * @return Redirect
     */
    public function destroy(CustomersNationality $customersNationality)
    {
        $customersNationality->delete();

        Toastr::success(trans('menu.user') . trans('flash.deleted') . trans('flash.successfully'), 'success');

        return redirect()->route('customers-nationalities.index');
    }
}
