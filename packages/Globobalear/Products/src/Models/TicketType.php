<?php

namespace Globobalear\Products\Models;


use App\ReservationTicket;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketType extends BaseModel
{
    const ADU = 1;

    use SoftDeletes;
    protected $fillable = ['title', 'acronym', 'take_place','sort', 'pirates_ticket_type_id'];
    protected $casts = ['take_place' => 'boolean'];

    /** RELATIONS */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function reservationTickets()
    {
        return $this->hasMany(ReservationTicket::class);
    }

    public function productSeatTypes()
    {
        return $this->belongsToMany(ProductSeatType::class, 'product_prices')->withPivot('price');
    }

    public function passSeatTypes()
    {
        return $this->belongsToMany(PassSeatType::class, 'passes_prices')->withPivot('price');
    }

    public function piratesTicketType()
    {
        return $this->belongsTo(TicketTypePirates::class, 'pirates_ticket_type_id');
    }
}
