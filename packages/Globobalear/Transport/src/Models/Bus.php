<?php

namespace Globobalear\Transport\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Globobalear\Products\Models\Pass;

class Bus extends Model
{
    use SoftDeletes;
    protected $fillable = ['capacity', 'transporter_id', 'route_id', 'pass_id'];
    protected $appends = ['occupied'];

    /** ACCESORS */
    public function getOccupiedAttribute()
    {
        $this->load('reservationTransports');

        return $this->reservationTransports->sum('quantity');
    }

    /** RELATIONS */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function transporter()
    {
        return $this->belongsTo(Transporter::class);
    }

    public function pass()
    {
        return $this->belongsTo(Pass::class);
    }

    public function reservationTransports()
    {
        return $this->hasMany(ReservationTransport::class);
    }


    /** METHODS */

    /**
     * Generate transport tickets for a reservation
     *
     * @param array $ticketsData
     * @param Model $reservation
     * @return Collection
     */
    public static function generateReservationTransports(array $ticketsData, Model $reservation): Collection
    {
        $reservation->reservationTransports()->delete();
        $reservationTransport = collect();

        if(isset($ticketsData['transport'])) {
            foreach ($ticketsData['transport'] as $transport) {
                if ((bool)$transport['quantity']) {
                    $pickupPoint = Bus::with('route')->find($transport['bus_id'])
                        ->route->pickupPoints()->find($transport['pickup_point_id']);

                    $reservationTransport[] = $reservation->reservationTransports()->create([
                        'reservation_id' => $reservation->id,
                        'bus_id' => $transport['bus_id'],
                        'quantity' => $transport['quantity'],
                        'price' => $pickupPoint->pivot->price,
                        'pickup_point' => $pickupPoint->name,
                        'pickup_point_id' => $pickupPoint->id,
                        'hour' => $pickupPoint->pivot->hour,
                    ]);
                }
            }
        }
       
        return collect($reservationTransport);
    }
}
