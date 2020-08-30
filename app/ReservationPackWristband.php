<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Globobalear\Wristband\Models\WristbandPass;
use DB;

class ReservationPackWristband extends Model
{
    //use HasTicketType, HasSeatType;

    protected $fillable = ['pack_id','wristband_passes_id','reservation_id', 'quantity', 'unit_price'];
    //protected $dates = ['datetime'];

    public function getPriceWODiscount()
    {
        return $this->quantity * $this->unit_price;
    }

    public function getTotalPriceAttribute()
    {
        $pricePlusUnits = $this->getPriceWODiscount();
        $totalPrice = round($pricePlusUnits - ($pricePlusUnits * $this->unit_discount / 100), 2);
        return $totalPrice;
    }


    public function getTitleWristbandAttribute()
    {
       
        if(!empty($this->wristbands)){
        return $this->wristbands->title .' | '. $this->wristbands->date_start .' | '. $this->wristbands->date_end;
        }
    }



   
    /** RELATIONS */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function wristbands()
    {
        return $this->belongsTo(WristbandPass::class,'wristband_passes_id');
    }

    


}
