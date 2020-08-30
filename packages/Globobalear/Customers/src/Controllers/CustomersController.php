<?php

namespace Globobalear\Customers\Controllers;


use App\Helpers\Helper;
use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        $query = Customer::select('customers.id as id', 'customers.name as name', 'email', 'phone')->get();

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
        $customershowyoumeetus = CustomersHowYouMeetUs::pluck('name', 'id')->toArray();

        return view('customers::customers.create')
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
            'email' => 'required|email|unique:customers,email',
        ]);

        $inputRequest = Helper::saveUncheckedCheckbox($request->all(), ['is_enabled', 'newsletter', 'resident']);

        $inputRequest = Helper::carbonParse($inputRequest, 'd-m-Y', ['birth_date']);

        Customer::create($inputRequest);

        Toastr::success('Customers create successfully', 'Created!');

        return redirect()->route('customers.index');
    }

    /**
     * Display th e specified resource.
     *
     * @param Customer $customer
     * @return Redirect
     */
    public function show(Customer $customer)
    {
        return redirect()->route('customers::customers.edit', $customer->id);
    }


    /**
     * Display th e specified resource.
     *
     * @param Customer $customer
     * @return Redirect
     */
     public function reservations($id)
     {
         
        $customer = Customer::find($id);
        $reservations = $customer->reservations;
        //$this->dataReservations($id);
        
        return view('customers::customers.reservations')
        ->with('customer', $customer);
     }

     public function dataReservations($id, Datatables $datatables)
     {
        $customer = Customer::find($id);
        $reservations = $customer->reservations;
       
         return $datatables->collection($reservations)
             ->addColumn('action', 'customers::customers.actionsreservations')
             ->make(true);
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
        $customershowyoumeetus = CustomersHowYouMeetUs::pluck('name', 'id')->toArray();

        return view('customers::customers.edit')
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
            'email' => 'required|email|unique:customers,email,' . $customer->id,
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


    /************** AJAX CALLS **************/

    /**
     * Get List of Customers filtered by param
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request) : JsonResponse
    {
        $customers = Customer::where('name', 'like', "%{$request->q}%")
            ->orWhere('email', 'like', "%{$request->q}%")->get();

        $return['items'] = $customers;

        return response()->json($return);
    }

    /**
     * Get Customer by ID
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request) : JsonResponse
    {
        $customer = Customer::find($request->id);

        return response()->json($customer);
    }
}
