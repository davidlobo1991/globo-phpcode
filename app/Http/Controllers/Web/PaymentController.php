<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;

use Illuminate\Support\Collection;

use Illuminate\Contracts\Support\Renderable;

use App\Models\Web\Cart;

use App\Http\Controllers\Controller;

use App\Http\Controllers\Traits\ReservationsManager;

class PaymentController extends Controller
{
    use ReservationsManager;

    /**
     * Creates new payment
     *
     * @param \Illuminate\Http\Request $request the request
     *
     * @return mixed or null
     */
    public function create(Request $request) // : ? mixed
    {
        $cart = Cart::catch();

        if (!$cart) {
            return back()->withErrors(['error' => ['Error on proccess cart.']]);
        }

        $cart->setForm($request);
        $cart->save();

        //Generar reservas para el carrito
        $referenceNumber = date('ymdHis');
        $cart->setReferenceNumber($referenceNumber);

        $reservationsCreated = $this->createReservations($cart);

        $response = null;

        $paymentMethod = $request->payment_option;

        switch ($paymentMethod) {
            case 'paypal':
                $paypalController = new PayPalController();
                $response = $paypalController->createPayment($cart);
                break;
            case 'card':
            case 'tpv':
                $tpvController = new TpvController();
                $response = $tpvController->getBookingPayment($cart, $reservationsCreated);
                break;
            default:
                return back()->withErrors(['error' => ['Payment method not found.']]);
        }

        return $response;
    }

    /**
     * Creates a new reservation
     *
     * @param \App\Models\Web\Cart $cart the cart
     *
     * @return array
     */
    private function createReservations(Cart $cart) : array
    {
        //reservation for pirates
        $reservationsShowPirates = $this->createReservationShowPirates($cart);

        //create reservation for local globo
        $reservationsProducts = $this->createReservationProducts($cart);

        //create reservation pack
        $reservationsPacks = $this->createReservationPacks($cart);

        //create reservation wristbands
        // $reservationsWristbands = $this->createReservationWristbands($cart);

        //return toodas las reservas[]
        $reservationsGlobo = array_merge($reservationsProducts, $reservationsPacks['reservations']);
        // $reservationsGlobo = array_merge($reservationsGlobo, $reservationsWristbands);

        return [
            'reservations_pirates' => array_merge($reservationsShowPirates, $reservationsPacks['reservations_pirates']),
            'reservations_globo' => $reservationsGlobo
        ];
    }


    /**
     * Static view for correct payment
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function urlOK() : Renderable
    {
        return view('Web.payments.tpv-ok');
    }

    /**
     * Static view for wrong payment
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function urlKO() : Renderable
    {
        return view('Web.payments.tpv-ko');
    }
}
