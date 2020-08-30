<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class ReservationType extends Model
{
    const ERROR = 0;
    const PRODUCTS = 1;
    const PACKS = 2;
    const WRISTBANDS = 3;
   
    const ERROR_ROUTE = 'reservations';
    const PRODUCTS_ROUTE = 'reservations';
    const PACKS_ROUTE = 'reservationspacks';
    const WRISTBANDS_ROUTE = 'reservations-wristbands';
}
