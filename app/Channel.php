<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = ['name'];

    protected $with = ['viewreservation'];

    /** RELATIONS */
    public function passesSeller()
    {
        return $this->belongsTo(PassesSeller::class);
    }

    public function reservations()
    {
        return $this->hasMany('App\Models\Reservations');
    }

    
    public function viewreservation()
    {
        return $this->hasMany(ViewReservation::class);
    }


    public function reservations_tickets()
    {
        return $this->hasManyThrough('App\Models\ReservationsTickets', 'App\Models\Reservations');
    }
}
