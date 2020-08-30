<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GlobalConf extends Model
{
    //Constantes usadas en las reservas de carrito
    const CHANNEL =  6; //Online
    const CREATED_BY_PIRATES = 21; //User from pirates database
    const CREATED_BY_GLOBO = 3; //User from globobalear crs

    protected $table = 'global_conf';
    protected $primaryKey = 'id';

    public $fillable = ['amber_trigger', 'family_discount', 'gold_discount',
        'booking_fee', 'pound_exchange','paypal'];
}
