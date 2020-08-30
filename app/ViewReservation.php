<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Globobalear\Payments\Traits\HasPayments;
use Globobalear\Promocodes\Traits\HasPromocodes;
use Globobalear\Products\Traits\HasPass;
use Globobalear\Transport\Traits\HasTransport;
use Globobalear\Transport\Models\ReservationTransport;
use App\ReservationType;
use Globobalear\Menus\Traits\HasMenu;
use Illuminate\Database\Eloquent\SoftDeletes;
use Globobalear\Menus\Models\ReservationMenu;
use Globobalear\Customers\Models\Customer;
use Globobalear\Products\Models\Product;
use Globobalear\Products\Models\Pass;
use Globobalear\Promocodes\Models\Promocode;
use Globobalear\Packs\Models\Pack;
use Illuminate\Support\Facades\DB;
use Globobalear\Wristband\Models\WristbandPass;
use Illuminate\Support\Collection;
use App\Http\Controllers\Traits\HasEmail;

//TODO Mira si tienes errores al extender, cosa que no tendria que pasar, ahora hereda los metodos de Reservation porque los necesito
class ViewReservation extends Reservation
{
    //use HasEmail;
    use SoftDeletes;
    protected $table = 'viewreservations';
    protected $fillable = [];
    protected $appends = [];
    protected $dates = [];


    protected $with = ['reservationTickets','reservationMenu','ReservationTransport','reservationPacks','ReservationPacksPirates','reservationWristbandPasses','reservationPacksWristband','promocodes', 'pass', 'product'];
    //protected $appends = ['totalPrice', 'pendingToPay'];
    //protected $dates = ['created_at', 'canceled_date'];
    /** ACCESSORS & MUTATORS */



    /** RELATIONS */

    public function packs()
    {
        return ($this->belongsTo(Pack::class,'pack_id','id'));
    }

    public function ReservationTransport(): HasMany
    {
        return $this->hasMany(ReservationTransport::class,'reservation_id','id');
    }

    public function reservationTickets(): HasMany
    {
        return $this->hasMany(ReservationTicket::class,'reservation_id','id');
    }

    public function reservationPacks(): HasMany
    {
        return $this->hasMany(ReservationPack::class,'reservation_id','id');
    }

    public function ReservationPacksPirates(): HasMany
    {
        return $this->hasMany(ReservationPackPirates::class,'reservation_id','id');
    }

    public function reservationPacksWristband(): HasOne
    {
        return $this->hasOne(ReservationPackWristband::class,'reservation_id','id');
    }

    public function reservationWristbandPasses(): belongsToMany
    {
        return $this->belongsToMany(WristbandPass::class, 'reservation_wristband_pass','reservation_id' )->withPivot('quantity')->withTimestamps();
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class,'id','id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function pass()
    {
        return $this->belongsTo(Pass::class, 'pass_id', 'id');
    }


    public function reservationMenu(): HasMany
    {
        return $this->hasMany(ReservationMenu::class,'reservation_id','id');
    }

     public function promocodes()
    {
        return $this->hasOne(Promocode::class, 'id', 'promocode_id');
    }

    public static function getTotalADUSum($pass = 0)
    {
        $return = DB::select('CALL  sp_reservations_total_passe (?)', [$pass]);
        $return = Collection::make($return);
        $return = $return->sum('ADU');

        return $return;
    }

    public static function getTotalCHDSum($pass = 0)
    {
        $return = DB::select('CALL  sp_reservations_total_passe (?)', [$pass]);
        $return = Collection::make($return);
        $return = $return->sum('CHD');

        return $return;
    }

    public static function getTotalINFSum($pass = 0)
    {
        $return = DB::select('CALL  sp_reservations_total_passe (?)', [$pass]);
        $return = Collection::make($return);
        $return = $return->sum('INF');

        return $return;
    }

    public static function getTotalTOTSum($pass = 0)
    {
        $return = DB::select('CALL  sp_reservations_total_passe (?)', [$pass]);
        $return = Collection::make($return);
        $return = $return->sum('TOT');

        return $return;
    }

    public static function getAvailabilityPack($pass = 0, $reservation = 0)
    {
        $return = DB::select('CALL sp_reservations_total_pack(?,?)', [$pass, $reservation]);
        $return = Collection::make($return);
        //$return = $return->sum('ADU');
        return $return;

    }



}
