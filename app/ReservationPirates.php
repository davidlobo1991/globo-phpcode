<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Globobalear\Products\Models\PassPirates;
use Globobalear\Payments\Models\PaymentMethodPirates;
// use Globobalear\Transport\Models\ReservationTransport;

class ReservationPirates extends Model //Reservatio
{
    protected $connection = 'pirates';
    protected $table = 'reservations';


    public $fillable = [
        "reference_number",
        "reservation_number",
        "ticket_number",
        "name",
        "surname",
        "phone",
        "email",
        "idcard",
        "shows_id",
        "shows_title",
        "passes_id",
        "passes_datetime",
        "marketings_id",
        "reconcile",
        "invoice",
        "comments",
        "created_by", // creado por el usuario X
        "canceled_by",
        "canceled_date",
        "created_as", // creado para el el usuario X
        "customers_id",
        "booking_fee",
        "has_gold_discount",
        "promocodes_id",
        "promocode_code",
        "wheelchair_quantity",
        "vegetarian_quantity",
        "reservation_channel_id",
        "reseller_id",
        "status",
        "comments_transport",
        'finished_by',
        'ambertrigger',
        'reconciled_by',
        'reconciled_at',
        'unfinished_by',
        'unfinished_date',
        'unfinished_reason',
        'changepasse_by',
        'changepasse_date',
        'reservation_globobalear_id',
    ];

    /** RELATIONS */
    public function paymentsMethods()
    {
        return $this->belongsToMany(PaymentMethodPirates::class, 'payments_reservations', 'reservations_id', 'payment_method_id', 'reservations_id')
            ->withPivot('total', 'reference', 'paid_by')->withTimestamps();
    }

    public function reservationTickets()
    {
        return $this->hasMany(ReservationTicketPirates::class, 'reservations_id');
    }

    public function reservationTransports()
    {
        return $this->hasMany(ReservationTransport::class);
    }

    public function pass()
    {
        return $this->belongsTo(PassPirates::class, 'passes_id');
    }

    /** ACCESSORS & MUTATORS */
    public function getTotalPriceAttribute(): float
    {
        $total = 0;
        $total += $this->booking_fee;
        $total += $this->ticketsPrice;
        // $total += $this->transportPrice;
        // $total -= $this->getPromocodeAttribute($total);
        $total += $this->tax($total);

        return $total;
    }

    public function tax($total): float
    {
        if ($this->paypal > 0) {
            return $total * $this->paypal /100;
        }
        return 0;
    }

    public function getTicketsPriceAttribute(): float
    {
        return $this->reservationTickets->map(function ($item) {
            return $item->quantity * $item->unit_price;
        })->sum();
    }

    /**
     *
     */
    public function getNameReservationAttribute()
    {
        return $this->pass->show->title . ' | '. $this->pass->datetime;
    }

    // public function getTransportPriceAttribute(): float
    // {

    //     return $this->reservationTransports->map(function($item) {
    //        return $item->quantity * $item->price;
    //     })->sum();
    // }

    // public function getPromocodeAttribute($total): float
    // {
    //     return $total * $this->discount /100;
    // }

    // public function promocode(): BelongsTo
    // {
    //     return $this->belongsTo(Promocode::class);
    // }
}
