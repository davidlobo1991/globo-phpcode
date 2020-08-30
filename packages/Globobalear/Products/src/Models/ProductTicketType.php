<?php

namespace Globobalear\Products\Models;

use Spatie\Translatable\HasTranslations;

class ProductTicketType extends BaseModel
{
    use HasTranslations;

    protected $table = 'product_ticket_type';
    protected $fillable = [];
    protected $with = ['product', 'ticketType'];

    /** RELATIONS */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

}
