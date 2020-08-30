<?php

namespace Globobalear\Payments\Controllers;


use App\Http\Notification\Facade\Toastr;
use App\Reservation;
use Globobalear\Products\Models\Pass;
use Globobalear\Products\Models\Product;
use Globobalear\Packs\Models\Pack;
use Globobalear\Wristband\Models\WristbandPass;
use App\ReservationPirates;
use App\User;
use App\ViewPayment;
use App\GlobalConf;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Traits\HasEmail;
use Globobalear\Payments\Models\PaymentMethod;
use Globobalear\Payments\Models\PaymentMethodPirates;
use Globobalear\Payments\Models\PaymentMethodReservation;
use Globobalear\Payments\Models\PaymentReservationPirates;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;
use Auth;
use Log;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{

    use HasEmail;

     /**
     * Display a listing of the resource.
     * @return View
     */
     public function index(Request $request): View
     {
         if(Auth::user()->role->role_permission('view_payment')){

            $passes = Pass::orderBy('datetime')->get()->pluck('title','id')->prepend('All', 0);

            $packs = Pack::orderBy('title')->get()->pluck('title','id')->prepend('All', 0);
            $products = Product::orderBy('name')->get()->pluck('name','id')->prepend('All', 0);
            $wristbandPass = WristbandPass::orderBy('title')->get()->pluck('title','id')->prepend('All', 0);

            $users = User::orderBy('name')->get()->pluck('name','id')->prepend('All', 0);
            $paymentMethods= PaymentMethod::get();

            $payments = ViewPayment::query();

            $viewType = isset($request->viewType) ? $request->viewType : 'method';

            if (isset($request->pass) && $request->pass <> 0) {
                $payments->orwhere('pass_id', $request->pass);
            }

            if (isset($request->pack) && $request->pack <> 0) {
                $payments->orwhere('pack_id', $request->pack);
            }

            if (isset($request->wristband) && $request->wristband <> 0) {
                $payments->orwhere('wristaband_id', $request->wristband);
            }

            if (isset($request->product) && $request->product <> 0) {
                //BUSCA EN LOS PACKS QUE SE TENGAN SHOWS
                $payments->whereHas('reservation.reservationPacks', function ($query) use ($request) {
                    $query->where('product_id', $request->product);

                });
                $payments->orwhere('product_id', $request->product);


            }

            $allDates = $request->date == '';
            if (isset($request->date) && !empty($request->date) && !$allDates) {
                $init = Carbon::createFromFormat('d-m-Y', $request->date)->startOfDay()->addHours(2);
                $end = Carbon::createFromFormat('d-m-Y', $request->date)->endOfDay()->addHours(2);
                $payments->whereBetween('created_at', [$init, $end]);
            } else {
                $init = Carbon::now()->startOfDay()->addHours(2);
                $end = Carbon::now()->endOfDay()->addHours(2);
            }

            if (isset($request->user) && $request->user <> 0) {
                $payments->where('user_id', $request->user);
            }

            $payments = $payments->paginate(20);
            return view('payments::payments.index')

            ->with('payments', $payments)
            ->with('viewType', $viewType)
            ->with('request', $request)
            ->with('paymentmethods', $paymentMethods)
            ->with('passes', $passes)
            ->with('packs', $packs)
            ->with('products', $products)
            ->with('wristbandPass', $wristbandPass)
            ->with('users', $users);

         }else{
             abort(403);
         }
     }




    /**
     * Show the view to make payments for a reservation passed by get
     *
     * @param $reservationId
     * @return View
     */
    public function getPayments(Request $request, int $reservationId): View
    {
        if(Auth::user()->role->role_permission('view_payment')){
            try {

                $this->validate($request, [
                    'payment.*' => 'required|numeric',
                    'payment.*' => 'numeric',

                ]);
                $reservation = Reservation::with('pass.product')->with('pack')->findOrFail($reservationId);


                if(!$reservation->reservationTickets->isEmpty()){
                    $paid= $reservation->remainderToPayInteger();
                }

                if(!$reservation->ReservationPacksPirates->isEmpty()){
                    $paid= $reservation->remainderToPayPackInteger();
                }

                if(!$reservation->reservationPacks->isEmpty()){
                    $paid= $reservation->remainderToPayPackInteger();
                }

                if(!$reservation->reservationWristbandPasses->isEmpty()){
                    $paid= $reservation->remainderToPayWristbandInteger();
                }

                $paymentMethods = PaymentMethod::with(['reservations' => function ($q) use ($reservation) {
                        $q->where('reservations.id', $reservation->id);
                }])->get();

            } catch (ModelNotFoundException $e) {
                Toastr::error('Payment not found', 'Error!');
                return redirect()->back();
            }

            $paymentMethods = PaymentMethod::get();
            $global = GlobalConf::first();

            return view('payments::payments.paymentReservation')
                ->with('paid', $paid)
                ->with('global', $global)
                ->with('paymentMethods', $paymentMethods)
                ->with('reservation', $reservation);
        }else{
            abort(403);
        }
    }

    /**
     * Process to save the payments and totals into the reservation
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postPayments(Request $request): RedirectResponse
    {
        if(Auth::user()->role->role_permission('create_payment')){

            $reservation = Reservation::find($request->reservationId);

            if (!isset($request->payment))
                return redirect()->route('payments.index');

            foreach ($request->payment as $payment => $quantities) {
                $paymentMethod = PaymentMethod::with('reservations')->find($payment);

                $quantities = collect($quantities);
                $qty = $quantities->sum();

                if ($qty > 0) {
                    $paymentMethod->reservations()->attach($reservation->id,
                        ['total' => $qty, 'user_id' => \Auth::user()->id]);
                }
            }

            if(!$reservation->reservationTickets->isEmpty()){
                    if (number_format((float)$reservation->remainderToPay(), 2) != 0.0) {
                    Toastr::success('Error Payment', 'Error!');
                    return redirect()->route('payments.getPayments', $reservation->id);
                }
            }

            if(!$reservation->reservationPacks->isEmpty()){
                if (number_format((float)$reservation->remainderToPayPack(), 2) != 0.0) {
                Toastr::success('Error Payment', 'Error!');
                return redirect()->route('payments.getPayments', $reservation->id);
            }
        }

            // TODO se usará en caso que se deba pagar PAYPAL desde el CRS
            /*if($reservation->payment_methods->pop()->id == 6){
                $global = GlobalConf::first();
                $reservation->paypal = $global->paypal;;
            }
            else{
                $reservation->paypal = 0;
            }*/

            $reservation->finished = 1;
            $reservation->finished_by = \Auth::user()->id;
            $reservation->save();

            // START METHOD RESERVATION IN PIRATES
            if(!$reservation->ReservationPacksPirates->isEmpty()){
                foreach ($reservation->ReservationPacksPirates as $pirates){

                    $reservationPirates = ReservationPirates::where('reservation_globobalear_id',$reservation->id)->where('shows_id',$pirates->show_id)->first();
                    // verified booking fee
                    if($reservation->booking_fee){
                       $total = ($reservation->booking_fee + $pirates->unit_price) * $pirates->quantity;
                    }
                    else{
                        $total = $reservationPirates->unit_price * $reservationPirates->quantity;
                    }
                    //add paid
                    $payment_verified= PaymentReservationPirates::where('reservations_id',$reservationPirates->id)->first();
                    if(!$payment_verified){
                        $paymentMethodReservation = PaymentReservationPirates::create([
                            'reservations_id' => $reservationPirates->id,
                            'payment_method_id' => 7, // Payment magaluf
                            'total' => $total,
                            'reference' => 'Payment from Magaluf Pack',
                            'paid_by' => 21 //Admin globobalear 'admin@globobalear.com'

                            ]);
                        // add reservation paid
                        $reservationPirates->status = 1;
                        $reservationPirates->save();
                    }

                }
            }
             // END METHOD RESERVATION IN PIRATES

            try{
                $this->sendMailReservation($reservation);
                $this->sendMailReservationProviders($reservation);
            }
            catch(\Exception $e){
                Toastr::success('Reservation created but failed Send Email', 'Success!');
                return redirect()->route('payments.getPayments', $reservation->id);
            }


            Toastr::success('Reservation created and paid successfully', 'Success!');

            return redirect()->route('payments.getPayments', $reservation->id);

        }else{
            abort(403);
        }
    }

    public function removePayment (Request $request,Reservation $reservation)
    {
        if(Auth::user()->role->role_permission('delete_payment')){

            $paymentMethod = PaymentMethodReservation::destroy($request->id)? 1 : 0;

            if($paymentMethod ==  1){
                $paid =  PaymentMethodReservation::onlyTrashed()->find($request->id);
                $reservations = Reservation::find($paid->reservation_id);
                $reservations->finished = 0;
                $paid->removed_by = \Auth::user()->id;
                $paid->removed_date = Carbon::now()->toDateTimeString();
                $paid->removed_reason = $request->reason;
                $paid->save();
                $reservations->save();
            }
            return $paymentMethod;
        }else{
            abort(403);
        }

    }

    public function excelPayments(Request $request)
    {
        if(!Auth::user()->role->role_permission('download_payment'))
            abort(403, 'Unauthorized');

        $paymentMethods= PaymentMethod::get();
        $payments = ViewPayment::query();

        if (isset($request->pass) && $request->pass <> 0) {
            $payments->orwhere('pass_id', $request->pass);
        }

        if (isset($request->pack) && $request->pack <> 0) {
            $payments->orwhere('pack_id', $request->pack);
        }

        if (isset($request->wristband) && $request->wristband <> 0) {
            $payments->orwhere('wristaband_id', $request->wristband);
        }

        if (isset($request->product) && $request->product <> 0) {
            //BUSCA EN LOS PACKS QUE SE TENGAN SHOWS
            $payments->whereHas('reservation.reservationPacks', function ($query) use ($request) {
                $query->where('product_id', $request->product);
            });

            $payments->orwhere('product_id', $request->product);
        }

        $allDates = $request->date == '';
        if (isset($request->date) && !empty($request->date) && !$allDates) {
            $init = Carbon::createFromFormat('d-m-Y', $request->date)->startOfDay()->addHours(2);
            $end = Carbon::createFromFormat('d-m-Y', $request->date)->endOfDay()->addHours(2);
            $payments->whereBetween('created_at', [$init, $end]);
        } else {
            $init = Carbon::now()->startOfDay()->addHours(2);
            $end = Carbon::now()->endOfDay()->addHours(2);
        }

        if (isset($request->user) && $request->user <> 0) {
            $payments->where('user_id', $request->user);
        }

        $payments = $payments->get();

        Excel::create('Payments-'.Carbon::now(), function ($excel) use ($payments,$paymentMethods) {
            $excel->sheet('Payments', function ($sheet) use ($payments,$paymentMethods) {
                $sheet->loadView('payments::payments.excel')->with("payments", $payments)->with("paymentmethods", $paymentMethods);
            });
        })->download('xls');


    }

    public function xmlPayments(Request $request)
    {
        if(!Auth::user()->role->role_permission('download_payment'))
            abort(403, 'Unauthorized');

        $payments = ViewPayment::query();

        if (isset($request->pass) && $request->pass <> 0) {
        $payments->where('pass_id', $request->pass);
        }

        $allDates = $request->date == '';
        if (isset($request->date) && !empty($request->date) && !$allDates) {
        $carbonDate = Carbon::createFromFormat('d-m-Y', $date);
        $init = Carbon::createFromFormat('d-m-Y', $request->date)->startOfDay()->addHours(2);
        $end = Carbon::createFromFormat('d-m-Y', $request->date)->endOfDay()->addHours(2);
        $payments->whereBetween('created_at', [$init, $end]);
        } else {
        $carbonDate = Carbon::now();
        $init = Carbon::now()->startOfDay()->addHours(2);
        $end = Carbon::now()->endOfDay()->addHours(2);
        }

        if (isset($request->user) && $request->user <> 0) {
        $payments->where('user_id', $request->user);
        }
        $payments = $payments->groupBy('reservation_id');
        $payments = $payments->get();

        $sxe = new SimpleXMLElement('<XML></XML>');

        $sxe->addChild('Fecha', $carbonDate->format('d/m/Y'));
        $cobrosDia = $sxe->addChild('CobrosDia');

        foreach ($payments as $key => $reservas) {


            $booking = $cobrosDia->addChild('Booking');
            $booking->addChild('CodBooking', $reservas->reservation_id);
            $booking->addChild('FechaProduct', $reservas->pass_datetime);
            $booking->addChild('CodEspectaculo', $reservas->pass_id);
            $booking->addChild('Importe', $reservas->total);

            $cobros = $booking->addChild('Cobros');

                if ($reservas->payment_method_id != 5) {
                    $cobro = $cobros->addChild('Cobro');
                    $cobro->addChild('Tipo', $reservas->method);
                    $cobro->addChild('Importe', $reservas->total);

            }
        }

        Storage::put('payments-'.Carbon::now()->format('d-m-Y').'.xml', $sxe->asXML());

        return response()->download(storage_path('app/payments-'.Carbon::now()->format('d-m-Y').'.xml'))->deleteFileAfterSend(true);
    }
}
