<?php

namespace Globobalear\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use App\ReservationPirates;
use Globobalear\Payments\Models\PaymentMethodPirates;

class PaymentReservationPirates extends Model
{
    
    protected $connection = 'pirates';
    protected $table = 'payments_reservations';
    //protected $primaryKey = 'id';

    protected $fillable = ['payment_method_id','reservations_id','total','reference','paid_by'];

    public function reservation()
    {
        return $this->belongsTo(ReservationPirates::class, 'reservations_id', 'id');
    }

       public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethodPirates::class, 'payment_method_id', 'id');
    }

 
}
