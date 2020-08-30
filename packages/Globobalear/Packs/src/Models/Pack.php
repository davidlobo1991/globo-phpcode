<?php

namespace Globobalear\Packs\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Globobalear\Promocodes\Models\Promocode;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Globobalear\Packs\Models\PackProduct;
use App\ReservationPack;
use App\ReservationPackPirates;
use App\ReservationPackWristband;

class Pack extends Model
{
    use HasTranslations;
    use SoftDeletes;

    protected $fillable = ['title', 'acronym','date_start','date_end'];

    protected $dates = ['date_start', 'date_end'];

    protected $with = ['packline','packlinePirates','packlineWristband'];

    public function getNameAttribute()
    {
        return $this->title . ' | ' . $this->date_start->format('d/m/Y') . ' | ' . $this->date_end->format('d/m/Y');
    }

    /** METHODS */

    /**
     * @return mixed
     */
    public function getPriceAttribute()
    {
        $price = $this->packline->sum('price') +
            $this->packlinePirates->sum('price') +
            ($this->packlineWristband->price ?? 0);

        return $price;
    }

    public function getDateStartFormatedAttribute()
    {
        if ($this->date_start) {
            return $this->date_start->format('d/m/Y');
        }
    }

    public function getDateEndFormatedAttribute()
    {
        if ($this->date_end) {
            return $this->date_end->format('d/m/Y');
        }
    }

    /** METHODS */

    /**
     * @param array $data
     * @param Model $reservation
     * @return Collection
     */
    public static function generateReservationPacks(Request $request, Model $reservation): Collection
    {

        $reservation->reservationPacks()->delete();
        $packs = collect();
        foreach ($request->el as $product => $value) {
            if ((bool)$request->quantity) {
                $packs[] = ReservationPack::create([
                    "pack_id" => $request->pack,
                    "product_id"   => $request->products[$value],
                    "seat_type_id"   => $request->seattypes[$value],
                    "ticket_type_id"   => $request->tickettypes,
                    "unit_price"   => $request->price[$value],
                    'reservation_id' => $reservation->id,
                    'pass_id' => $request->pass[$value],
                    'quantity' => is_null($request->quantity) ? 0 : $request->quantity,
                ]);
            }
        }

        return collect($packs);
    }

    /**
     * @param array $data
     * @param Model $reservation
     * @return Collection
     */
    public static function generateReservationPacksPirates(Request $request, Model $reservation): Collection
    {

        $packs = collect();
        $reservation->reservationPacksPirates()->delete();
        foreach ($request->elpirates as $show => $value) {
            if ((bool)$request->quantity) {
                $packs[] = ReservationPackPirates::create([
                    "pack_id" => $request->pack,
                    "show_id"   => $request->showspirates[$value],
                    "seat_type_id"   => $request->seattypespirates[$value],
                    "ticket_type_id"   => $request->tickettypes,
                    "unit_price"   => $request->pricepirates[$value],
                    'reservation_id' => $reservation->id,
                    'pass_id' => $request->passpirates[$value],
                    'quantity' => is_null($request->quantity) ? 0 : $request->quantity,
                ]);
            }
        }


        return collect($packs);
    }


    /**
     * @param array $data
     * @param Model $reservation
     * @return Collection
     */
    public static function generateReservationPacksWristbands(Request $request, Model $reservation): Collection
    {

        $reservation->reservationPacksWristband()->delete();
        $packs = collect();
        if ((bool)$request->quantity) {
            $packs[] = ReservationPackWristband::create([
                "pack_id" => $request->pack,
                "wristband_passes_id"   => $request->wristband_passes_id,
                "unit_price"   => $request->pricewristband,
                'reservation_id' => $reservation->id,
                'quantity' => is_null($request->quantity) ? 0 : $request->quantity,
            ]);
        }


        return collect($packs);
    }





    public function setDateStartAttribute($value)
    {
        $this->attributes['date_start'] = Carbon::parse($value)->format('Y-m-d');
    }

    public function setDateEndAttribute($value)
    {
        $this->attributes['date_end'] = Carbon::parse($value)->format('Y-m-d');
    }


    public function getDateStartReservationsAttribute()
    {
        return Carbon::parse( $this->attributes['date_start'])->format('Y-m-d');
    }

    public function getDateEndReservationsAttribute()
    {
        return Carbon::parse( $this->attributes['date_end'])->format('Y-m-d');
    }



    /** RELATIONS */

    public function packline()
    {
        return $this->hasMany(PackProduct::class);
    }

    public function packlinePirates()
    {
        return $this->hasMany(PackShowPirates::class);
    }

    public function packlineWristband()
    {
        return $this->hasOne(PackWristband::class);
    }

    public function promocodes()
    {
        return $this->belongsToMany(Promocode::class, 'promocode_pack')->isValid();
    }

    public function veryfyCartPack($pack)
    {
        //todo, se puede dar mas seguridad pero bueno, no hay mas tiempo
        if(count($this->packline) == count($pack->products))
            if(count($this->packlinePirates) == count($pack->shows))
                if(count($this->packlineWristband) == count($pack->wristbands))
                    return true;

        return false;
    }
}
