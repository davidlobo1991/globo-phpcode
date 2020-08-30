<?php

namespace Globobalear\Customers\Controllers;


use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Customers\Models\Customer;
use Globobalear\Customers\Models\CustomersLanguage;
use Globobalear\Customers\Models\CustomersNationality;
use Globobalear\Customers\Models\CustomersHowYouMeetUs;
use Globobalear\Customers\Models\Gender;
use Yajra\Datatables\Datatables;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('customers::customers.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = Customer::select('customers.id as id', 'customers.name as name', 'email', 'phone_number')->get();

        return $datatables->collection($query)
            ->addColumn('action', 'customers::customers.actions')
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        $genders = Gender::pluck('name', 'id')->toArray();
        $customerslanguages = CustomersLanguage::pluck('name', 'id')->toArray();
        $customersnationality = CustomersNationality::pluck('name', 'id')->toArray();
        $customershowyoumeetus = CustomersHowYouMeetUs::pluck('name', 'id')->toArray();

        return view('customers::customers.create')
            ->with('customersnationality', $customersnationality)
            ->with('customerslanguages', $customerslanguages)
            ->with('customershowyoumeetus', $customershowyoumeetus)
            ->with('genders', $genders);
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
            'name' => 'required',
            'status' => 'required',
            'newsletter' => 'required',
            'resident' => 'required',
            'identification_number' => 'required|unique:customers,identification_number',
            'birth_date' => 'required|date',
            'phone_number' => 'required',
            'address' => 'required',
            'town' => 'required',
            'country' => 'required',
            'postal_code' => 'required',
            'email' => 'required|email|unique:customers,email',
            'gender_id' => 'required',
            'customer_language_id' => 'required',
            'customer_how_you_meet_us_id' => 'required',
        ]);

        //dd($request->all());
        Customer::create($request->all());

        Toastr::success('Customers create successfully');

        return redirect()->route('customers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Customer $customer
     * @return Redirect
     */
    public function show(Customer $customer)
    {
        return redirect()->route('customers::customers.edit', $customer->id);
    }

    /**
     * Pass the form for editing the specified resource.
     *
     * @param  Customer $customer
     * @return View
     */
    public function edit(Customer $customer)
    {
        $genders = Gender::pluck('name', 'id')->toArray();
        $customerslanguages = CustomersLanguage::pluck('name', 'id')->toArray();
        $customersnationality = CustomersNationality::pluck('name', 'id')->toArray();
        $customershowyoumeetus = CustomersHowYouMeetUs::pluck('name', 'id')->toArray();

        return view('customers::customers.edit')
            ->with('customersnationality', $customersnationality)
            ->with('customerslanguages', $customerslanguages)
            ->with('customershowyoumeetus', $customershowyoumeetus)
            ->with('customer', $customer)
            ->with('genders', $genders);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Customer $customer
     * @return Redirect
     */
    public function update(Request $request, Customer $customer)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
            'newsletter' => 'required',
            'resident' => 'required',
            'identification_number' => 'required|unique:customers,identification_number,' . $customer->id,
            'birth_date' => 'required|date',
            'phone_number' => 'required',
            'address' => 'required',
            'town' => 'required',
            'country' => 'required',
            'postal_code' => 'required',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'gender_id' => 'required',
            'customer_language_id' => 'required',
            'customer_how_you_meet_us_id' => 'required',
        ]);

        $customer->update($request->all());

        Toastr::success('Customer updated successfully');

        return redirect()->route('customers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Customer $customer
     * @return Redirect
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        Toastr::success(trans('menu.user') . trans('flash.deleted') . trans('flash.successfully'), 'success');

        return redirect()->route('customers.index');
    }
}
