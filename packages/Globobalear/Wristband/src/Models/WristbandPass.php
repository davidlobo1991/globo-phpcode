<?php

namespace Globobalear\Wristband\Models;

use App\Reservation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Globobalear\Products\Models\Product;
use Globobalear\Products\Models\ShowPirates;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class WristbandPass extends Model
{
    use HasTranslations;
    use SoftDeletes;
    protected $fillable = ['title', 'date_start', 'date_end', 'quantity', 'price', 'wristband_id'];


    public function wristband()
    {
        return $this->belongsTo(Wristband::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * Foreign key en la base de datos de pirates y la tabla NM
     */
    public function showsPirates()
    {
        return $this->belongsToMany(ShowPirates::class, 'wristband_pass_show_pirates', 'wristband_pass_id', 'shows_id');
    }

    public function setDateStartAttribute($value)
    {
        $this->attributes['date_start'] = Carbon::parse($value)->format('Y-m-d 00:00');
    }

    public function setDateEndAttribute($value)
    {
        $this->attributes['date_end'] = Carbon::parse($value)->format('Y-m-d 00:00');
    }

    public function getDateStartAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function getDateEndAttribute($value)
    {
        return Carbon::parse($value)->format('d-m-Y');
    }

    public function reservations(): HasMany
    {
        return $this->belongsToMany(Reservation::class);
    }

   

}
