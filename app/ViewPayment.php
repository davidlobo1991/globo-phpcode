<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\ReservationPack;
use App\ReservationPackPirates;
use App\Reservation;
class ViewPayment extends Model
{
    
    
    protected $table = 'viewpayment';
    protected $primaryKey = 'id';
    protected $dates = ['created_at', 'pass_datetime'];
    protected $with = ['reservation'];
    /** ACCESSORS & MUTATORS */
    
   /** RELATIONS */

   public function reservation()
   {
       return $this->hasOne(Reservation::class,'id','reservation_id');
   }

    public function getReservationTypeIdAttribute()
    {
        
        $variable = $this->attributes['reservation_type_id'];
        switch ($variable) {
            case ReservationType::PRODUCTS:
            $variable = ReservationType::PRODUCTS_ROUTE;
            break;
            
            case ReservationType::PACKS:
            $variable = ReservationType::PACKS_ROUTE;
            break;
            case ReservationType::WRISTBANDS:
            $variable = ReservationType::WRISTBANDS_ROUTE;
            break;
            
            
            default:
            $variable = ReservationType::ERROR_ROUTE;
            break;
        }
        
        
        return $variable;
    } 

   



   
}
