<?php

namespace Globobalear\Products\Models;

use App\ReservationTicket;

use Spatie\Translatable\HasTranslations;

use Illuminate\Database\Eloquent\SoftDeletes;

class SeatType extends BaseModel
{
    const GENERIC = 7;

    use HasTranslations,  SoftDeletes;

    public $translatable = [
        'description'
    ];

    protected $fillable = [
        'title',
        'acronym',
        'description',
        'default_quantity',
        'is_enable',
        'sort'
    ];

    protected $casts = [
        'is_enable' => 'boolean'
    ];

    /** RELATIONS */
    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('default_quantity');
    }

    public function reservationTickets()
    {
        return $this->hasMany(ReservationTicket::class);
    }

    public function passes()
    {
        return $this->belongsToMany(Pass::class)->using(PassSeatType::class)
            ->withPivot('seats_available');
    }

    public function passSeatTypePivot()
    {
        return $this->belongsTo(PivotPassSeatType::class, 'seat_type_id');
    }

    /** SCOPE */
    public function scopeEnable($query)
    {
        return $query->where('is_enable', 1);
    }
}
