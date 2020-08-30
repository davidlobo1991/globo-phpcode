<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Globobalear\Payments\Models\PaymentMethodReservation;


class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role_id', 'resellers_type_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

     /** RELATIONS */

    public function ResellerType()
    {
        return $this->belongsTo(ResellerType::class);
    }

    public function role()
	{
		return $this->belongsTo('App\Role');
    }
    
   
    public function payments()
    {
        return $this->hasMany(PaymentMethodReservation::class, 'user_id', 'id');
    }

   
}
