<?php

namespace App\Http\Controllers\Traits;

use App\ReservationPirates;
use App\ReservationTicketPirates;
use App\ReservationTicket;
use App\ReservationType;
use App\ReservationPack;
use App\ReservationPackPirates;
use App\ReservationPackWristband;
use App\GlobalConf;
use App\Reservation;
use App\CustomerPirates;
use App\Models\Web\Cart;
use Globobalear\Customers\Models\Customer;
use Globobalear\Products\Models\PassPirates;
use Globobalear\Products\Models\Pass;
use Globobalear\Packs\Models\Pack;
use Globobalear\Payments\Traits\PromocodeSecure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

trait ReservationsManager
{
    use PromocodeSecure;

    /**
     * @param Cart $cart
     * @return array
     */
    public function createReservationShowPirates(Cart $cart, $pack = null)
    {
        $reservationsStored = [];
        $shows = $cart->shows;
        if ($pack) {
            $shows =  $pack->shows ?? [];
        }

        foreach ($shows as $product) {
            $passPirates = PassPirates::find($product->passId);
            if (!$passPirates) {
                continue;
            }

            //Promocode descartado por el momento
            //$promoCode = $this->getVerifiedPromocode($cart->promoCode, $ReservationType::PRODUCTS, $passPirates->id);
            $request_reservation = $this->getRequestForReservationPirates($passPirates, $cart);

            $customer = $this->getCustomerPirates($cart);
            $request_reservation['customers_id'] = $customer->id;
            $request_reservation['surname'] = $cart->getFormAttribute('last_name');

            $reservation = ReservationPirates::create($request_reservation);

            $request_tickets = $this->getRequestForTicketsPirates(
                $passPirates, $reservation->id,
                $product->quantity ?? $pack->quantity,
                $product->seatTypeId ?? $pack->seatTypeId,
                $product->ticketTypeId ?? $pack->ticketTypeId
            );
            ReservationTicketPirates::create($request_tickets);

            $reservationsStored[] = $reservation;
        }

        return $reservationsStored;
    }

    /**
     * @param Model $pass
     * @param Cart $cart
     * @param null $promoCode
     * @return array
     */
    private function getRequestForReservationPirates(Model $pass, Cart $cart, $promoCode = null)
    {
        $attributes = [
            'passes_id' => $pass->id,
            'reservation_number' => 'ME'. uniqid(),
            'reference_number' => $cart->getReferenceNumber(),
            'status' => 0,
            'shows_id' => $pass->show->id,
            'shows_title' => $pass->show->title ?? null,
            'passes_datetime' => $pass->datetime ?? null,
            'created_by' => GlobalConf::CREATED_BY_PIRATES,
            'reservation_channel_id' => GlobalConf::CHANNEL,
            'booking_fee' => $cart->getBookinFee(),
        ];

        if ($promoCode) {
            $attributes["promocode"] = $promoCode->code;
            $attributes["promocode_id"] = $promoCode->id;
            $attributes["discount"] = $promoCode->id;//TODO
        }

        return array_merge_recursive($attributes, $cart->getForm());
    }

    /**
     * @param Model $realPass
     * @param int $reservationId
     * @param int $quantity
     * @param int $seatTypeId
     * @param int $ticketTypeId
     * @return array
     */
    private function getRequestForTicketsPirates(Model $realPass, int $reservationId, int $quantity, int $seatTypeId, int $ticketTypeId)
    {
        return [
            'quantity' => $quantity,
            'seat_types' => $seatTypeId,
            'ticket_types' => $ticketTypeId,
            'pass' => $realPass->title,
            'passes' => $realPass->id,
            'unit_price' => $realPass->getPriceByParams($ticketTypeId, $seatTypeId), //($realPass instanceof Pass) ? $realPass->getPriceByParams($ticketTypeId, $seatTypeId) : 0, //TODO get price pirates
            'reservations_id' => $reservationId,
            'ticket_discount_types_id' => ReservationTicketPirates::TICKET_DISCOUNT_TYPE,

            /**parte globo*/
            'seat_type_id' => $seatTypeId,
            'ticket_type_id' => $ticketTypeId,
            'reservation_id' => $reservationId,
        ];
    }

    /**
     * @param Cart $cart
     * @return array
     */
    public function createReservationProducts(Cart $cart)
    {
        $reservationsStored = [];
        foreach ($cart->products as $product) {
            try {
                $pass = Pass::findOrFail($product->passId);
            } catch (\Exception $ex) {
                continue;
            }
            $promoCode = $this->getVerifiedPromocode($cart->getPromocode(), ReservationType::PRODUCTS, $product);

            $request_reservation = $this->getRequestForReservation($pass, $cart, $promoCode);

            $customer = $this->getCustomer($cart);
            $request_reservation['customer_id'] = $customer->id;

            $reservation = Reservation::create($request_reservation);

            $request_tickets = $this->getRequestForTickets($pass, $reservation->id, $product->quantity, $product->seatTypeId, $product->ticketTypeId);
            ReservationTicket::create($request_tickets);

            $reservationsStored[] = $reservation;
        }

        return $reservationsStored;
    }

    /**
     * @param Model $pass
     * @param Cart $cart
     * @param null $promoCode
     * @return array
     */
    private function getRequestForReservation(Model $pass, Cart $cart, $promoCode = null)
    {
        $attributes = [
            'passes_id' => $pass->id,
            "pass_id" => $pass->id,
            'reservation_number' => 'ME'. uniqid(),
            'reference_number' => $cart->getReferenceNumber(),
            'status' => 0,
            'finished' => 0,
            'product_id' => $pass->product->id,
            'products' => $pass->product->id,
            'products_title' => $pass->show->title ?? null,
            'passes_datetime' => $pass->datetime ?? null,
            'reservation_type_id' => ReservationType::PRODUCTS,
            'created_by' => ($pass instanceof Pass) ? GlobalConf::CREATED_BY_GLOBO : GlobalConf::CREATED_BY_PIRATES,
            'reservation_channel_id' => GlobalConf::CHANNEL,
            "channel_id" => GlobalConf::CHANNEL,
            'booking_fee' => $cart->getBookinFee()
        ];

        if ($promoCode) {
            $attributes["promocode"] = $promoCode->code;
            $attributes["promocode_id"] = $promoCode->id;
            $attributes["discount"] = $promoCode->id;//TODO
        }

        return array_merge_recursive($attributes, $cart->getForm());
    }

    /**
     * @param Model $realPass
     * @param int $reservationId
     * @param int $quantity
     * @param int $seatTypeId
     * @param int $ticketTypeId
     * @return array
     */
    private function getRequestForTickets(Model $realPass, int $reservationId, int $quantity, int $seatTypeId, int $ticketTypeId)
    {
        return [
            'quantity' => $quantity,
            'seat_types' => $seatTypeId,
            'ticket_types' => $ticketTypeId,
            'pass' => $realPass->title,
            'passes' => $realPass->id,
            'unit_price' => $realPass->getPriceByParams($ticketTypeId, $seatTypeId), //($realPass instanceof Pass) ? $realPass->getPriceByParams($ticketTypeId, $seatTypeId) : 0, //TODO get price pirates
            'reservations_id' => $reservationId,
            'ticket_discount_types_id' => ReservationTicketPirates::TICKET_DISCOUNT_TYPE,

            /**parte globo*/
            'seat_type_id' => $seatTypeId,
            'ticket_type_id' => $ticketTypeId,
            'reservation_id' => $reservationId,
        ];
    }

    /**
     * @param Cart $cart
     */
    public function createReservationPacks(Cart $cart)
    {
        $reservationPirates = [];
        $reservationGlobo = [];

        foreach ($cart->packs as $pack) {
            $realPack = Pack::find($pack->packId);
            $promoCode = $this->getVerifiedPromocode($cart->getPromocode(), ReservationType::PACKS, $pack);

            //generate reservation globo (Pack $pack, float $bookingFee, $promoCode)//$pack,
            $request_reservation = $this->getRequestForReservationPack($realPack, $cart, $promoCode);

            $customer = $this->getCustomer($cart);
            $request_reservation['customer_id'] = $customer->id;

            $reservationGlobo[] = $reservation = Reservation::create($request_reservation);

            //generate reservation for wristband
            /*if ($realPack->packlineWristband and $pack->wristbands) {
                $request_reservation_wristband = $this->getRequestForReservationWristband($cart, $promoCode);

                $customer = $this->getCustomer($cart);
                $request_reservation_wristband['customer_id'] = $customer->id;

                $reservationWristbandGlobo = Reservation::create($request_reservation_wristband);

                $reservationWristbandGlobo->reservationWristbandPasses()->attach([
                    $pack->wristbands->wristband_pass_id => ['quantity' => $pack->quantity]
                ]);
            }*/

            //Insert passes of pack in reservation_pack[globo and pirates]
            $reservationsPacks = $this->generateReservationPack($realPack, $pack, $reservation);

            //generate reservations in pirates
            $reservationsPirates_ = $this->createReservationShowPirates($cart, $pack);

            $reservationPirates = array_merge($reservationsPacks, $reservationsPirates_);
        }

        return [
            'reservations_pirates' => $reservationPirates,
            'reservations' => $reservationGlobo
        ];
    }

    /**
     * @param Pack $pack
     * @param Cart $cart
     * @param $promoCode
     * @return array
     */
    private function getRequestForReservationPack(Pack $pack, Cart $cart, $promoCode)
    {
        $attributes = [
            'reservation_number' => 'MEP'. uniqid(),
            'reference_number' => $cart->getReferenceNumber(),
            'created_by' => GlobalConf::CREATED_BY_GLOBO,
            'reservation_channel_id' => GlobalConf::CHANNEL,
            "channel_id" => GlobalConf::CHANNEL,
            'status' => 0,
            'reservation_type_id' => ReservationType::PACKS,

            /** Parte de Globo reservations */
            "pack_id" => $pack->id,
            'booking_fee' => $cart->getBookinFee()
        ];

        if ($promoCode) {
            $attributes["promocode"] = $promoCode->code;
            $attributes["promocode_id"] = $promoCode->id;
            $attributes["discount"] = $promoCode->id;//TODO
        }

        return array_merge_recursive($attributes, $cart->getForm());
    }

    /**
     * @param Cart $cart
     */
    /*public function createReservationWristbands(Cart $cart)
    {
        $reservationsStored = [];
        foreach ($cart->wristbandPasses as $product) {
            $pass = WristbandPass::find($product->passId);
            $promoCode = $this->callSecuredPromocode($cart->getPromocode(), $pass->id, ReservationType::WRISTBANDS);

            $request_reservation_wristband = $this->getRequestForReservationWristband($cart, $promoCode);

            $customer = $this->getCustomer($cart);
            $request_reservation_wristband['customer_id'] = $customer->id;

            $reservationWristbandGlobo = Reservation::create($request_reservation_wristband);
            $reservationWristbandGlobo->reservationWristbandPasses()->attach([
                $pass->id => ['quantity' => $product->quantity]
            ]);
            $reservationsStored[] = $reservationWristbandGlobo;
        }

        return $reservationsStored;
    }*/

    /**
     * @param Cart $cart
     * @param $promoCode
     * @return array
     */
    // private function getRequestForReservationWristband(Cart $cart, $promoCode)
    // {
    //     $attributes = [
    //         'reference_number' => $cart->getReferenceNumber(),
    //         'reservation_number' => 'MEP'. uniqid(),
    //         'reservation_type_id' => ReservationType::WRISTBANDS,
    //         'status' => 0,

    //         "channel_id" => GlobalConf::CHANNEL,
    //         "created_by" => GlobalConf::CREATED_BY_GLOBO,

    //         'booking_fee' => $cart->getBookinFee(),
    //         'paypal' =>$cart->getPaypalTax()
    //     ];

    //     if ($promoCode)
    //     {
    //         $attributes["promocode"] = $promoCode->code;
    //         $attributes["promocode_id"] = $promoCode->id;
    //         $attributes["discount"] = $promoCode->id;//TODO
    //     }

    //     return array_merge_recursive($attributes, $cart->getForm());
    // }

    /**
     * @param Pack $realPack
     * @param $pack
     * @param Reservation $reservation
     * @return array
     */
    private function generateReservationPack(Pack $realPack, $pack, Reservation $reservation)
    {
        $reservationPack = [];
        $reservationPackPirates = [];
        // $reservationPackWristband = [];

        //verify if corrupted pack
        if (!$realPack->veryfyCartPack($pack)) {
            return [
                'reservation_packs' => [],
                // 'reservation_pack_wristbands' => [],
                'reservation_packs_pirates' => []
            ];
        }

        foreach ($pack->products as $product) {
            $reservationPack[] = ReservationPack::create([
                "pack_id" => $realPack->id,
                "product_id" => $product->showId,
                "seat_type_id" => $product->seatTypeId,
                "ticket_type_id" => $product->ticketTypeId,
                "unit_price" => $product->price ?? 0,
                'reservation_id' => $reservation->id,
                'pass_id' => $product->passId,
                'quantity' => $pack->quantity,
            ]);
        }

        foreach ($pack->shows as $show) {
            $reservationPackPirates[] = ReservationPackPirates::create([
                "pack_id" => $realPack->id,
                "show_id" => $show->showId, //$product->show_id,
                "seat_type_id" => $show->seatTypeId,
                "ticket_type_id" => $show->ticketTypeId,
                "unit_price" => $show->price ?? 0,
                'reservation_id' => $reservation->id,
                'pass_id' => $show->passId,
                'quantity' => $pack->quantity,
            ]);
        }

        /*if ($realPack->packlineWristband) {
            $reservationPackWristband[] = ReservationPackWristband::create([
                "pack_id" => $realPack->packlineWristband->pack_id,
                "wristband_passes_id" => $realPack->packlineWristband->wristband_passes_id,
                "unit_price" => $realPack->packlineWristband->price,
                'reservation_id' => $reservation->id,
                'quantity' => $pack->quantity,
            ]);
        }*/

        return [
            'reservation_packs' => $reservationPack,
            // 'reservation_pack_wristbands' => $reservationPackWristband,
            'reservation_packs_pirates' => $reservationPackPirates
        ];
    }

    public function getCustomer(Cart $cart)
    {
        return Customer::firstOrCreate([
            'email' => $cart->getFormAttribute('email')
        ], [
            'name' => $cart->getFormAttribute('name'),
            'surname' => $cart->getFormAttribute('last_name'),
            'phone' => $cart->getFormAttribute('phone')
        ]);
    }

    public function getCustomerPirates(Cart $cart)
    {
        return CustomerPirates::firstOrCreate([
            'email' => $cart->getFormAttribute('email')
        ], [
            'name' => $cart->getFormAttribute('name'),
            'surname' => $cart->getFormAttribute('last_name'),
            'phone' => $cart->getFormAttribute('phone')
        ]);
    }
}
