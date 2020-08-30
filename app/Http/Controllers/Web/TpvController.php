<?php

namespace App\Http\Controllers\Web;

use Log;
use App\Reservation;
use App\ReservationPirates;
use App\Models\Web\Cart;
use Carbon\Carbon;
use Ssheduardo\Redsys\Facades\Redsys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Globobalear\Payments\Models\PaymentMethod;
use Globobalear\Payments\Models\PaymentMethodPirates;
use App\Http\Controllers\Traits\HasEmail;

class TpvController
{
    use HasEmail;

    /**
     * Método que devuelve la vista con la información final de reserva y el formulario de Redsys
     * @param  Request $request - Recibe parámetros POST con los parámetros seleccionados
     * @return Vista
    */

    public function getBookingPayment(Cart $cart,$reservationsCreated)
    {
        $currentDate = Carbon::now();

        try {
            $key = config('redsys.key');
            $orderTPV = date('ymdHis');
            Redsys::setAmount($cart->getSubtotal());

            Redsys::setOrder($orderTPV);
            Redsys::setMerchantcode('346098700'); //Reemplazar por el código que proporciona el banco
            Redsys::setCurrency('978');
            Redsys::setTransactiontype('0');
            Redsys::setTerminal('1');
            Redsys::setMethod('T'); //Solo pago con tarjeta, no mostramos iupay
            Redsys::setNotification(route('payment-notification')); //Url de notificacion

            Redsys::setUrlOk(route('payment.url-ok')); //Url OK
            Redsys::setUrlKo(route('payment.url-ko')); //Url KO
            Redsys::setVersion('HMAC_SHA256_V1');
            Redsys::setTradeName('Magaluf');
            Redsys::setTitular('Magaluf');
            Redsys::setProductDescription('Ticket Booking');
            Redsys::setEnviroment('test'); //Entorno test / live

            Redsys::setAttributesSubmit($name = 'btn_submit', $id = 'btn_submit', $value = 'Pay Now Caixa Bank', $style = '', $cssClass = 'c-card__btn c-btn c-title c-title--primary c-btn--full');

            $signature = Redsys::generateMerchantSignature($key);
            Redsys::setMerchantSignature($signature);
            $form = Redsys::createForm();

        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return view('Web.payments.tpv', compact('form', 'cart','reservationsCreated'));
    }

    /**
     * Método que trata la notificación de pago de Redsys. Si es correcto, se actualiza la reserva.
     * @param  Request $request - Recibe parámetros POST con los parámetros seleccionados
     * @return Boolean
     */
    public function paymentNotification(Request $request)
    {
        Log::info($request->all());


        $key = config('redsys.key');
        $parameters = Redsys::getMerchantParameters($request->input('Ds_MerchantParameters'));
        $DsResponse = $parameters["Ds_Response"];
        $DsSecurePayment = $parameters["Ds_SecurePayment"];
        $DsOrder = $parameters['Ds_Order'];
        $price = $parameters['Ds_Amount'] / 100;

        $paymentOK = Redsys::check($key, $request);

        // dd($key, $parameters, $DsResponse, $DsSecurePayment, $DsOrder);

        if ($paymentOK) {
            if ($DsResponse == "0000" && $DsSecurePayment == "1") {
                $this->setReservationsAsFinished($DsOrder, $price);
            }
            if ($DsResponse == "9915" && $DsSecurePayment == "0") {
                $this->setReservationsAsCanceled($DsOrder);
            }
        }

        return response()->json(true);
    }

    public function setReservationsAsFinished($referenceNumber, $price) : void
    {
        $reservationsMallorca = Reservation::where('reference_number', $referenceNumber)->get();

        $reservationsPirates = ReservationPirates::where('reference_number', $referenceNumber)->get();

        // Comprobar que este finalizada
        if (!$this->checkReservations($reservationsMallorca, $reservationsPirates)) {
            \Log::info("There's reservations finisheds. Reference Number: ".$referenceNumber);
        }

        try {
            DB::beginTransaction();
            foreach ($reservationsMallorca as $reservation) {
                $reservation->payment_methods()->attach(
                    PaymentMethod::ONLINE, [
                        'total' => $price
                    ]
                );

                $reservation->update(
                    [
                        'finished' => true
                    ]
                );

            }

            foreach ($reservationsPirates as $reservation) {
                $reservation->paymentsMethods()->attach(
                    PaymentMethodPirates::ONLINE, [
                        'total' => $price
                    ]
                );

                $reservation->update(
                    [
                        'status' => 1
                    ]
                );
            }

            $this->sendMailReservation($reservationsMallorca->merge($reservationsPirates)->pluck('id'));
            $this->sendMailReservationProviders($reservationsMallorca->merge($reservationsPirates)->pluck('id'));

            DB::commit();
        } catch (\Exception $exception) {
            Log::info('PayPal Exception: '.$exception->getMessage());

            DB::rollBack();
        }
    }

    public function setReservationsAsCanceled($referenceNumber)
    {
        $reservationsMallorca = Reservation::where('reference_number', $referenceNumber)->get();

        $reservationsPirates = ReservationPirates::where('reference_number', $referenceNumber)->get();

        // Comprobar que este finalizada
        if (!$this->checkReservationsAreCanceled($reservationsMallorca, $reservationsPirates)) {
            \Log::info("There's reservations canceleds. Reference Number: ".$referenceNumber);
        }

        try {
            DB::beginTransaction();
            foreach ($reservationsMallorca as $reservation) {
                $reservation->update([
                    'canceled_by' => 2,
                    'canceled_date' => Carbon::now()->toDateTimeString(),
                    'canceled_reason' => 'Error pago Redsys',
                ]);
            }

            foreach ($reservationsPirates as $reservation) {
                $reservation->update([
                    'canceled_by' => 2,
                    'canceled_date' => Carbon::now()->toDateTimeString(),
                    'cancel_reason' => 'Error pago Redsys',
                ]);
            }
            DB::commit();
        } catch (\Exception $exception) {
            Log::info('PayPal Exception: '.$exception->getMessage());
            DB::rollBack();
        }
    }

    public function checkReservations($reservationsMallorca, $reservationsPirates)
    {
        if ($reservationsMallorca->count() == 0 && $reservationsPirates->count() == 0) {
            return false;
        }
        if ($reservationsMallorca->count() != 0 && $reservationsMallorca->where('finished', 1)->count() != 0) {
            return false;
        }
        if ($reservationsPirates->count() != 0 && $reservationsPirates->where('status', 1)->count() != 0) {
            return false;
        }
        return true;
    }

    public function checkReservationsAreCanceled($reservationsMallorca, $reservationsPirates)
    {
        if ($reservationsMallorca->count() == 0 && $reservationsPirates->count() == 0) {
            return false;
        }
        if ($reservationsMallorca->count() != 0 && $reservationsMallorca->reject(function ($reservation) {
            return $reservation->canceled_by == null;
        })->count() != 0) {
            return false;
        }
        if ($reservationsPirates->count() != 0 && $reservationsPirates->reject(function ($reservation) {
            return $reservation->canceled_by == null;
        })->count() != 0) {
            return false;
        }
        return true;
    }
}
