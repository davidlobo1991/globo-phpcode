<?php

namespace Globobalear\Customers\Controllers;


use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Customers\Models\CustomersLanguage;
use Yajra\Datatables\Datatables;

class CustomersLanguagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('customers::customerslanguages.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = CustomersLanguage::select('id','name')->get();

        $result = $datatables->collection($query)
            ->addColumn('action', 'customers::customerslanguages.actions')
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
        return view('customers::customerslanguages.create');
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

        CustomersLanguage::create($request->all());

        Toastr::success('Customer language create successfully');

        return redirect()->route('customers-languages.index');
    }

    /**
     * Display the specified resource.
     *
     * @param CustomerLanguage $customerLanguage
     * @return Redirect
     */
    public function show(CustomersLanguage $customersLanguage)
    {
        return redirect()->route('customers::customerslanguages.edit', $customersLanguage->id);
    }

    /**
     * Pass the form for editing the specified resource.
     *
     * @param  CustomerLanguage $customerLanguage
     * @return View
     */
    public function edit(CustomersLanguage $customersLanguage)
    {
        return view('customers::customerslanguages.edit')
            ->with('customerLanguage', $customersLanguage);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  CustomerLanguage $customerLanguage
     * @return Redirect
     */
    public function update(Request $request, CustomersLanguage $customersLanguage)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $customersLanguage->update($request->all());

        Toastr::success('Customer updated successfully');

        return redirect()->route('customers-languages.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  CustomerLanguage $customerLanguage
     * @return Redirect
     */
    public function destroy(CustomersLanguage $customersLanguage)
    {
        $customersLanguage->delete();

        Toastr::success(trans('menu.user') . trans('flash.deleted') . trans('flash.successfully'), 'success');

        return redirect()->route('customers-languages.index');
    }
}
