<?php

namespace Globobalear\Products\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use DB;

class PassPirates extends BaseModel
{
    protected $connection = 'pirates';
    public $table = "passes";
    protected $dates = ['datetime'];
    protected $appends = ['formattedDate'];

    /**
     * @return mixed
     */
    public function getFormattedDateAttribute()
    {
        return $this->datetime->format('d/m/Y H:i');
    }

    /**
     * @return BelongsTo
     */

     public function show(): BelongsTo
    {
        return $this->belongsTo(ShowPirates::class);
    }

    /**
     * @return string
     */

     public function getTitleAttribute()
    {
        return $this->show->title .'|'. Carbon::parse($this->datetime)->format('d-m-Y');
    }

    /** STORE PROCEDURE
     * @return string
     */

    public function getTotalSeats($passesSellerId= 1, $pass = 0 , $seattypes= 0  )
    {

        $return = DB::select('CALL '. env('PR_DATABASE') .'.sp_reservations_availability_pass_seattype(?,?,?)', [$passesSellerId,$pass,$seattypes]);

        return $return[0]->total_available_seats;
    }

    /** PROCEDURE
     * @* @param int $ticket_type
     * @* @param int $seat_type
     * @return float $price
     */
    public function getPriceByParams(int $ticket_type = TicketTypePirates::ADULT, int $seat_type = SeatTypePirates::GENERIC)
    {
        return DB::connection('pirates')->select('call pass_price (?,?,?)', [$this->id, $ticket_type, $seat_type])[0]->price ?? 0;
    }
}
