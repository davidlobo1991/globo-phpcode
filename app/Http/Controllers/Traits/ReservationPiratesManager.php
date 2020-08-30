<?php
/**
 * Created by PhpStorm.
 * User: moussa
 * Date: 30/11/17
 * Time: 9:52
 */
namespace App\Http\Controllers\Traits;

use App\Http\Requests\ReservationRequest;
use App\Reservation;
use App\ReservationPirates;
use App\ReservationTicketPirates;
use Globobalear\Resellers\Models\ResellerPirates;
use Globobalear\Products\Models\ShowPirates;
use Globobalear\Products\Models\PassPirates;
use Globobalear\Products\Models\SeatTypePirates;
use Globobalear\Products\Models\TicketTypePirates;


trait ReservationPiratesManager
{
    /**
     * @param ReservationRequest $request
     * @return array
     */
    public function storeReservationPirates(ReservationRequest $request, Reservation $reservation)
    {
        $request->request->add([
            'reservation_globobalear_id' => $reservation->id ?? null
            ]);
            $reservationPirates = [];
            $reservationTicketsPirates = [];
            
        //foreach($request->passpirates as $key => $pass){ porque está montado asi
        foreach ($request->elpirates as $pass => $value) {
            $request->request->add([
                'passes_id' => $request->passpirates[$value],
                'reservation_number' => 'GB'. uniqid() .$pass,
                'shows_id' => $request->showspirates[$value],
                'shows_title' => ShowPirates::find($request->showspirates[$value])->title ?? null,
                'passes_datetime' => PassPirates::find($request->passpirates[$value])->datetime ?? null,
                'created_by' => 21,//Admin globobalear 'admin@globobalear.com'
                'reservation_channel_id' => $request->channel_id,
                'status' => 0
            ]);

            $reservationPirates[] = $reservation = ReservationPirates::create($request->except('reseller_id'));
            $reservationTicketsPirates[] = ReservationTicketPirates::create([
                'quantity' => $request->quantity,
                'seat_types' => $request->seattypespirates[$value],
                'seat_type'=> SeatTypePirates::find($request->seattypespirates[$value])->title,
                'ticket_types' => $request->tickettypespirates[$value],
                'ticket_type'=> TicketTypePirates::find($request->tickettypespirates[$value])->title,
                'passes' => $request->passpirates[$value],
                'unit_price' => $request->pricepirates[$value],
                'reservations_id' => $reservation->id,
                'ticket_discount_types_id' => ReservationTicketPirates::TICKET_DISCOUNT_TYPE
            ]);
        }
      
        return $reservationPirates;
    }
}