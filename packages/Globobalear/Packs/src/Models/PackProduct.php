<?php

namespace Globobalear\Packs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Globobalear\Products\Models\Product;
use Globobalear\Products\Models\Pass;
use Globobalear\Products\Models\SeatType;
use Globobalear\Products\Models\TicketType;

class PackProduct extends Model
{

    protected $fillable = ['pack_id', 'product_id', 'seat_type_id', 'ticket_type_id', 'price'];
    public $timestamps = false;


    public function getTitleProductAttribute()
    {
        return $this->products->name;
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

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function passes()
    {
        return $this->hasMany(Pass::class, 'product_id', 'product_id');
    }


    public function seatType()
    {
        return $this->belongsTo(SeatType::class, 'seat_type_id');
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class, 'ticket_type_id');
    }
}
