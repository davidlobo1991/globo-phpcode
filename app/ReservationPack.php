<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Globobalear\Products\Models\Product;
use Globobalear\Products\Models\Pass;
use Globobalear\Products\Models\SeatType;
use Globobalear\Products\Models\TicketType;

class ReservationPack extends Model
{
    //use HasTicketType, HasSeatType;

    protected $fillable = ['pack_id','product_id','pass_id','ticket_type_id', 'seat_type_id', 'reservation_id', 'quantity', 'unit_price'];
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


    public function getTitleProductAttribute()
    {
        if(!empty($this->products)){
        return $this->products->name;
        }
    }


    public function getDatePassAttribute()
    {

        return $this->passes->datetime;
    }


    public function getTitleSeaTypeAttribute()
    {
        return $this->seatTypes->title;
    }

    public function getTitleTicketTypeAttribute()
    {
        return $this->ticketType->title;
    }



    /** RELATIONS */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function products()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function shows()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function passes()
    {
        return $this->belongsTo(Pass::class,'pass_id','id');
    }


    public function ticketType()
    {
        return $this->belongsTo(TicketType::class,'ticket_type_id');
    }

    public function seatTypes()
    {
        return $this->belongsTo(SeatType::class,'seat_type_id');
    }


}
