<?php

namespace Globobalear\Products\Models;

use Spatie\Translatable\HasTranslations;

class ProductSeatType extends BaseModel
{
    use HasTranslations;

    protected $table = 'product_seat_type';
    protected $fillable = [];
    protected $with = ['product', 'seatType', 'ticketTypes'];

    /** RELATIONS */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function seatType()
    {
        return $this->belongsTo(SeatType::class);
    }

    public function ticketTypes()
    {
        return $this->belongsToMany(TicketType::class, 'product_prices', 'seat_type_id', 'ticket_type_id')
            ->using(ProductsPrice::class)->withPivot('id', 'price');
    }

}
