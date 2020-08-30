<?php

namespace Globobalear\Payments\Models;


use App\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];

    const ONLINE = 3;

    /** RELATIONS */
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'payment_method_reservations')->withPivot('total','id')->withTimestamps();
    }


}
