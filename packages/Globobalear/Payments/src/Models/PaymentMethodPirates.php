<?php

namespace Globobalear\Payments\Models;

use Illuminate\Database\Eloquent\Model;
use App\ReservationPirates;

class PaymentMethodPirates extends Model
{

    protected $connection = 'pirates';
    public $table = 'payment_methods';
  //  protected $primaryKey = 'id';



    const CASH = 1;
    const PHYSICAL = 2;
    const ONLINE = 3;

    public function reservations()
    {
        return $this->belongsToMany(ReservationPirates::class, 'payments_reservations','id','id','reservations_id')
            ->withPivot('total','payment_method_id', 'reference', 'paid_by')->withTimestamps();
    }
}
