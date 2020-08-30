<?php

namespace Globobalear\Products\Models;

use Illuminate\Support\Collection;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;

class Pass extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'datetime',
        'product_id',
        'comment',
        'on_sale',
        'canceled_at',
    ];

    protected $dates = [
        'datetime',
        'canceled_at'
    ];

    protected $casts = [
        'product_id' => 'integer',
        'on_sale' => 'boolean',
    ];

    protected $appends = [
        'formattedDate'
    ];

    /** ACCESSORS AND MUTATORS */
    public function getFormattedDateAttribute()
    {
        return $this->datetime->format('d/m/Y H:i');
    }

    public function getTitleAttribute()
    {
        if (!empty($this->product)) {
            return $this->product->name . ' | ' . $this->datetime->format('d/m/Y H:i');
        }

        return "";
    }

    public function getProductNameAttribute()
    {
        if (!empty($this->product)) {
            return $this->product->name;
        }

        return "";
    }

    /** METHODS */

    /**
     * @param array $ticketsData
     * @param Model $reservation
     * @return Collection
     */
    public static function generateReservationTickets(array $ticketsData, Model $reservation): Collection
    {
        $reservation->reservationTickets()->delete();

        foreach ($ticketsData as $seatTypeId => $ticketTypes) {
            foreach ($ticketTypes as $ticketTypeId => $data) {
                if ((bool) $data['qty']) {
                    $tickets[] = config('crs.reservations-tickets-model')::create(
                        [
                            'ticket_type_id' => $ticketTypeId,
                            'seat_type_id' => $seatTypeId,
                            'reservation_id' => $reservation->id,
                            'quantity' => is_null($data['qty']) ? 0 : $data['qty'],
                            'unit_price' => is_null($data['price']) ? 0 : $data['price']
                        ]
                    );
                }
            }
        }

        return collect($tickets);
    }


    /** STORE PROCEDURE */
    public function getAvailabilitySum($pass = 0, $passesSellerId = 0)
    {
        $return = DB::select('CALL sp_reservations_availability(?,?)', [$this->id, $passesSellerId]);
        $return = Collection::make($return);
        $return = $return->sum('total_solded');

        return $return;
    }

    public function getAvailabilitySalesSum($pass = 0, $passesSellerId = 1)
    {
        $return = DB::select('CALL sp_reservations_availability(?,?)', [$this->id, $passesSellerId]);
        $return = Collection::make($return);
        $return = $return->sum('seats_sold');

        return $return;
    }

    public function getAvailability($pass = 0, $passesSellerId = 0)
    {
        $return = DB::select('CALL sp_reservations_availability(?,?)', [$this->id, $passesSellerId]);
        $return = Collection::make($return);
        return $return;
    }

    /** RELATIONS */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function show()
    {
        return $this->belongsTo(Product::class);
    }

    public function seatTypes()
    {
        return $this->belongsToMany(SeatType::class)->using(PassSeatType::class)
            ->withPivot('id', 'seats_available', 'seat_type_id')->enable();
    }

    /****/
    public function passes_seat_types()
    {
        return $this->hasMany(PivotPassSeatType::class, 'pass_id', 'id');
    }

    public function reservations()
    {
        return $this->hasMany(config('crs.reservations-model'));
    }

    public function getIsCanceledAttribute()
    {
        return ($this->on_sale == 0);
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeNotCanceled($query)
    {
        return $query->where('canceled_at', null)->orderBy('datetime');
    }

    public function scopeOnSale($query)
    {
        return $query->where('on_sale', 1);
    }

    public function scopeGetNextPasses($query)
    {
        //Restar hora por product->antelación
        $query->whereHas('product', function($query) {
            return $query->whereRaw("datetime >= ADDTIME(CURRENT_TIMESTAMP(), CONCAT(`products`.`limit_days`, ' ', `products`.`limit_hours`, ':00:00.00'))");
        });
        return $query;
    }

    public function hasThisDayPassed()
    {
        return Carbon::now() >= $this->datetime;
    }

    public function getDayAttribute($value)
    {
        return Carbon::parse($this->attributes['datetime'])->format('l');
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($this->attributes['datetime'])->format('l, j F H:i \h.');
    }

    public function getNextAttribute($value)
    {
        return Carbon::parse($this->attributes['datetime'])->format('l H:i \h.');
    }

    public function getSeatTypesWithOcupationAttribute()
    {
        $seatTypes = [];
        foreach($this->seatTypes as $key => $seatType){
            $seatTypes[$key] = $seatType;
            $seatTypes[$key]['pivot']['free_seats'] = $seatType->pivot->freeSeats;
            //$seatTypes[$key]['pivot']['seta_price'] = $seatType->pivot->filledSeats;
        }

        return $this->seatTypes;
    }

    public function scopeWithTicketPrices($query)
    {
        return $query->with(
            [
                'seatTypes' => function ($query) {
                    $query->with('passes');
                }
            ]
        );
    }


    /** PROCEDURE
     * @* @param int $ticket_type
     * @* @param int $seat_type
     * @return float $price
     */
    public function getPriceByParams(int $ticket_type, int $seat_type)
    {
        return \DB::select('call pass_price (?,?,?)',[$this->id, $ticket_type, $seat_type])[0]->price ?? 0;
    }
}
