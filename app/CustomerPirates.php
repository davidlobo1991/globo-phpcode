<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerPirates extends Model
{
    protected $connection = 'pirates';

    public $table = "customers";

	protected $fillable = [
        'name',
        'surname',
        'email',
        'phone',
        'accept_publicity'
	];

    public function reservations() {
        return $this->hasMany(ReservationPirates::class);
    }
}
