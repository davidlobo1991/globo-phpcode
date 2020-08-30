<?php

namespace App\Http\Controllers;

use App\Http\Notification\Facade\Toastr;
use App\PassesSeller;
use App\Reservation;
use App\Http\Controllers\Traits\ReservationPiratesManager;
use App\ViewReservation;
use App\GlobalConf;
use App\Channel;
use App\ReservationType;
use Globobalear\Customers\Models\Customer;
use Globobalear\Resellers\Models\Reseller;
use Globobalear\Packs\Models\Pack;
use Globobalear\Packs\Models\PackProduct;
use Globobalear\Packs\Models\PackShowPirates;
use Carbon\Carbon;
use App\Http\Requests\ReservationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Globobalear\Payments\Models\PaymentMethod;
use Globobalear\Products\Models\Pass;
use Globobalear\Transport\Models\Bus;
use Globobalear\Menus\Models\Menu;
use Yajra\Datatables\Datatables;
use App\Role;
use App\Permission;
use App\RolePermission;
use Barryvdh\DomPDF\Facade as PDF;
use Auth;
use Log;
use DB;

class ReservationsPacksController extends Controller
{
    use ReservationPiratesManager;
    /**
     * Resevations the form for creating a new resource.
     * @return View
     */
    public function index()
    {
        if(Auth::user()->role->role_permission('view_reservations')){
            return view('reservations.canceled');
        }else{
            abort(403);
        }
    }


    /**
     * Create the form for creating a new resource.
     * @param $passSellerId
     * @return View
     */
    public function create($passSellerId): View
    {

        if(Auth::user()->role->role_permission('create_reservations')){

            $passSeller = PassesSeller::with('channels')->find($passSellerId);

            $currentDate = Carbon::now();
            $pack = Pack::where('date_start', '<=', $currentDate->format('Y-m-d H:i:s'));

            $pack = $pack->orderBy('title')->select(DB::raw('id, CONCAT(title , "| between (" , date_format(date_start, "%d/%m/%Y"), " | " , date_format(date_end, "%d/%m/%Y"),") " )as title'))->pluck('title', 'id')->prepend('- Available Packs -',0);
            
            $channels = Channel::where('passes_seller_id',1)->where('is_enable',1)->pluck('name','id');
            $global = GlobalConf::first();
            return view('reservationspacks.create')
                ->with('global', $global)
                ->with('channels', $channels)
                ->with('passSeller', $passSeller)
                ->with('pack', $pack);
        }else{
            abort(403);
        }
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return RedirectResponse
     */
    public function store(ReservationRequest $request): RedirectResponse
    {
        if(! Auth::user()->role->role_permission('create_reservations')){
            return abort(403);
        }

        //Verifica si el customer existe
        if (empty($request->customer_id)) {
            $request->customer_id = $this->storeCustomer($request->all());
        }

        //dd($request->all());
        $reservation = Reservation::create($request->all());
       
       /** Start Reservations Packs */
       if (isset($request->pack_id)){
           $pack = Pack::find($request->pack);
           $reservation->pack_id = $pack->id;
           $reservation->reservation_type_id = ReservationType::PACKS;
           $reservation->save();
          

            //add line products
            if (isset($request->el)) {
                Pack::generateReservationPacks($request, $reservation);   
            }

            //add line shows
            if (isset($request->elpirates)) {
            Pack::generateReservationPacksPirates($request, $reservation);
            //store pirates reservation, el finish pago se hereda de reservations globo
            $this->storeReservationPirates($request, $reservation);
           
            }

            //add line wristbands
            if (isset($request->wristband_passes_id)) {
                Pack::generateReservationPacksWristbands($request, $reservation);   
            }
            
            if($reservation->pendingPackToPay) {
                $reservation->finished = 0;
                return redirect()->route('payments.getPayments', [$reservation->id]);
            }

        }

        Log::info('Create reservations Pack. #' . trans('common.reservation_number') .':'. $reservation->reservation_number .' # User By: ' .\Auth::user()->name );

        Toastr::success('Reservation created successfully', 'Created!');

        return redirect()->route('reservations.index');

    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function storeCustomer($inputData)
    {
        $flag= 0;
        $inputData = [
            'name' => $inputData['name'],
            'phone' => $inputData['phone'],
            'email' => $inputData['email'],
            'identification_number' => $inputData['identification_number']
        ];

        $customer = Customer::where('email',$inputData['email'])->first();
        if(!isset($customer))
        {
            $customer = Customer::create($inputData);
            $flag= 1;
        }

        return $customer->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reservation $reservation
     * @return RedirectResponse
     */
    public function show($id)
    {

        if(Auth::user()->role->role_permission('show_reservations')){
            $reservation =  Reservation::find($id);
            $pack= Pack::find($reservation->pack_id);
            // dd($reservation);
            return view('reservationspacks.product')
                ->with('reservation', $reservation)
                ->with('pack', $pack)

                ;
        }else{
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reservation $reservation
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()->role->role_permission('edit_reservations')){
            $reservation =  Reservation::find($id);

            $reservation->load('channel.passesSeller', 'pass.show');
            $pack = Pack::orderBy('title')->pluck('title', 'id')->prepend('- Available Packs -',0);
            $passSeller = $reservation->channel->passesSeller;
            $channels = Channel::where('passes_seller_id',1)->where('is_enable',1)->pluck('name','id');
            $resellers = Reseller::where('passes_seller_id', $passSeller->id)->pluck('company', 'id');
            $global = GlobalConf::first();

            return view('reservationspacks.edit')
                ->with('global', $global)
                ->with('pack', $pack)
                ->with('channels', $channels)
                ->with('passSeller', $passSeller)
                ->with('reservation', $reservation)
                ->with('resellers', $resellers);
        }else{
            abort(403);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Reservation $reservation
     * @return \Illuminate\Http\Response
     */
    public function update(ReservationRequest $request, Reservation $reservation)
    {
        if(! Auth::user()->role->role_permission('create_reservations')){
            return abort(403);
        }

        $reservation =  Reservation::find($request->reservation_id);
        $reservation->update($request->all());
        $reservation->save();

        /** Start Reservations Packs */
        if (isset($request->pack_id)){
            $pack = Pack::find($request->pack);
            $reservation->pack_id = $pack->id;
            $reservation->reservation_type_id = ReservationType::PACKS;
            $reservation->save();
            Pack::generateReservationPacks($request, $reservation,$pack);

            if($reservation->pendingPackToPay) {
                $reservation->finished = 0;
                return redirect()->route('payments.getPayments', [$reservation->id]);
            }

        }

        Toastr::success('Reservation Pack updated successfully', 'Updated!');

        return redirect()->route('reservations.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reservation $reservation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reservation $reservation)
    {
        if(Auth::user()->role->role_permission('delete_reservations')){
            $reservation->delete();
            Toastr::success('Reservation ' . trans('flash.deleted') . trans('flash.successfully'), 'success');
            return redirect()->route('reservations.index');
        }else{
            abort(403);
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reservation $reservation
     * @return \Illuminate\Http\Response
     */
    public function pdf(Reservation $reservation,$id)
    {
        if(Auth::user()->role->role_permission('show_reservations')){
            $reservation =  Reservation::find($id);
            $pack= Pack::find($reservation->pack_id);

            /*return view('reservations.pdf')
            ->with('reservation', $reservation);*/

            $pdf = PDF::loadView('reservationspacks.pdf', [
                'reservation' => $reservation,
                'pack' => $pack,
            ]);
            return $pdf->stream('reservationspacks.pdf');

        }else{
            abort(403);
        }
    }
}
