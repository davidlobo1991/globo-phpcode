<?php

namespace App\Http\Controllers\Traits;

use App\Reservation;
use App\ReservationPirates;

use Illuminate\Support\Facades\Mail;

use Illuminate\Mail\Message;

use Barryvdh\DomPDF\Facade as PDF;

trait HasEmail
{

    /**
     * @param Reservation $reservation
     * @return bool
     * @throws \Throwable
     */
    protected function sendMailReservation(Reservation $reservation)
    {
        $htmlView = view('Email.pdf', ['reservation' => $reservation])->render();
        $htmlView = preg_replace('/>\s+</', '><', $htmlView);
        $pdf = PDF::loadHtml($htmlView);
        $view= $pdf->stream('invoice.pdf'); 

        if (!empty($reservation->email)) {
            Mail::send(
                'Email.index', ["reservation" => $reservation],  function ($message) use ($reservation,$view) {
                    $fileName = 'magalufessential_'.date('Y-m-d').'.pdf';
                    $message->attachData($view, $fileName);
                    $message->to($reservation->email)
                        ->subject('Reservations | RESERVATION NUMBER:' . $reservation->reservation_number);
                }
            );
        }
        if (Mail::failures()) {
            return false;
        }

        return true;
    }

    /**
     * @param Reservation $reservation
     * @return bool
     * @throws \Throwable
     */
    protected function sendMailReservationPirates(ReservationPirates $reservation)
    {

        $htmlView = view('Email.pirates.pdf', ['reservation' => $reservation])->render();
        $htmlView = preg_replace('/>\s+</', '><', $htmlView);
        $pdf = PDF::loadHtml($htmlView);
        $view= $pdf->stream('invoice.pdf');

        if (!empty($reservation->email)) {
            Mail::send('Email.pirates.index', ["reservation" => $reservation], function ($message) use ($reservation, $view) {
                $fileName = 'magalufessential_'.date('Y-m-d').'.pdf';
                $message->attachData($view, $fileName);
                $message->to($reservation->email)
                    ->subject('Reservations | RESERVATION NUMBER:' . $reservation->reservation_number);
            });
        }
        if (Mail::failures()) {
            return false;
        }
        return true;
    }

    protected function sendMailReservationProviders(Reservation $reservation) : bool
    {
        try {
            $providers = $reservation->getProviders();
            foreach ($providers as $provider) {
                Mail::send('Email.providers', compact('reservation', 'provider'), function (Message $message) use ($reservation, $provider) {
                    $message->to($provider->email)
                            ->subject('Reservations | RESERVATION NUMBER:' . $reservation->reservation_number);
                });
            }

            return true;
        } catch (Exception $e) {
            Log::error($e->getMessage());

            return false;
        }
    }
}
