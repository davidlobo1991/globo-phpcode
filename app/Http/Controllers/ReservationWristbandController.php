<?php

namespace App\Http\Controllers;

use App\Channel;
use App\GlobalConf;
use App\Http\Requests\ReservationRequest;
use App\PassesSeller;
use App\Reservation;
use App\ReservationType;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Globobalear\Customers\Models\Customer;
use Globobalear\Resellers\Models\Reseller;
use Globobalear\Wristband\Models\Wristband;
use Globobalear\Wristband\Models\WristbandPass;
use App\Http\Notification\Facade\Toastr;

class ReservationWristbandController extends Controller
{

    /**
     * Show the form for creating a new resource.
     * @param $passSellerId
     * @return View
     */
    public function create(): View
    {
        if(!Auth::user()->role->role_permission('create_reservations'))
            abort(403, 'Unauthorized');

        $passSeller = PassesSeller::with('channels')->find(PassesSeller::DIRECT);
        $wristbandPasses = WristbandPass::orderBy('title')->pluck('title', 'id')->prepend('- Available Wristband Passes -',0);
        $channels = Channel::where('passes_seller_id', $passSeller->id)->where('is_enable',1)->pluck('name','id');
        $global = GlobalConf::first();
        $resellers = Reseller::pluck('company', 'id');
        $wristbands = Wristband::pluck('title', 'id');

        return view('reservationsWristbands.create', compact('global', 'channels', 'passSeller', 'wristbandPasses', 'resellers', 'wristbands'));
    }

    /**
     * @param ReservationRequest $request
     * @return RedirectResponse
     */
    public function store(ReservationRequest $request): RedirectResponse
    {
        if(! Auth::user()->role->role_permission('create_reservations')){
            return abort(403);
        }

        $customer = Customer::firstOrCreate($request->only('name', 'phone', 'email', 'identification_number'));
        $request->request->add(['customer_id' ,$customer->id]);

        $reservation = Reservation::create($request->except(['finished']));
        $reservation->reservationWristbandPasses()->attach($request->wristbands);
        
        $reservation->reservation_type_id = ReservationType::WRISTBANDS;
        $reservation->save();

        if($reservation->pendingToPayWristbandPass) {
            return redirect()->route('payments.getPayments', [$reservation->id]);
        }

        return redirect()->route('reservations.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reservation $reservation
     * @return RedirectResponse
     */
    public function show($id)
    {
        if(! Auth::user()->role->role_permission('view_reservations')){
            return abort(403);
        }

        $reservation = Reservation::find($id);
        if(!Auth::user()->role->role_permission('show_reservations'))
            abort(403);

        return view('reservationsWristbands.show', compact('reservation'));
    }

    /**
     * @param Reservation $reservation
     * @param $id
     * @return mixed
     */
    public function pdf($id)
    {
        if(!Auth::user()->role->role_permission('show_reservations'))
            abort(403);

        $reservation = Reservation::find($id);

        $pdf = PDF::loadView('reservationsWristbands.pdf', compact('reservation'));

        return $pdf->stream('reservation.pdf');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function edit($id)
    {
        if(!Auth::user()->role->role_permission('edit_reservations'))
            abort(403);

        $reservation = Reservation::find($id);

        $passSeller = PassesSeller::with('channels')->find(PassesSeller::DIRECT);
        $wristbandPasses = WristbandPass::orderBy('title')->pluck('title', 'id')->prepend('- Available Wristband Passes -', 0);
        $channels = Channel::where('passes_seller_id', $passSeller->id)->where('is_enable', 1)->pluck('name','id');
        $global = GlobalConf::first();
        $resellers = Reseller::pluck('company', 'id');
        $wristbands = Wristband::pluck('title', 'id');

        return view('reservationsWristbands.edit',
            compact('global', 'channels', 'passSeller', 'wristbandPasses', 'resellers', 'wristbands', 'reservation')
        );
    }

    public function update(ReservationRequest $request, $id)
    {
        if(! Auth::user()->role->role_permission('create_reservations')){
            return abort(403);
        }

        $reservation = Reservation::find($id);

        $data = [];
        foreach ($request->wristbands as $key => $pass){
            $data[$key]['wristband_pass_id'] = array_pop($pass);
            $data[$key]['quantity'] = $reservation->reservationWristbandPasses[$key]->pivot->quantity;
        }

        $reservation->reservationWristbandPasses()->sync($data);

        Toastr::success("Reservation $reservation->id updated successfully", 'Updated!');

        return redirect()->route('reservations.index');
    }
}
