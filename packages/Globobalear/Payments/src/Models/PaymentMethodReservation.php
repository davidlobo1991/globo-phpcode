<?php

namespace Globobalear\Payments\Models;


use App\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ReservationType;

class PaymentMethodReservation extends Model
{
    use SoftDeletes;

    protected $fillable = ['user_id','payment_method_id','reservation_id','total','removed_by','removed_date','removed_reason'];


    /** RELATIONS */
    public function reservations()
    {
        return $this->belongsTo(Reservation::class)->withPivot('total')->withTimestamps()->whereNull('payment_method_reservations.deleted_at');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'id');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }



}
