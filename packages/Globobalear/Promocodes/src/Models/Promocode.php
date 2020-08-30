<?php namespace Globobalear\Promocodes\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Support\Collection;

use Illuminate\Support\Facades\DB;

use Globobalear\Packs\Models\Pack;

use Globobalear\Products\Models\Pass;
use Globobalear\Products\Models\Product;

use Globobalear\Wristband\Models\Wristband;

class Promocode extends Model
{
    protected $fillable = [
        'code',
        'valid_from',
        'valid_to',
        'for_from',
        'for_to',
        'discount',
        'single_use',
        'canceled'
    ];

    protected $casts = [
        'canceled' => 'boolean',
        'single_use' => 'boolean',
        'discount' => 'integer'
    ];

    protected $dates = [
        'valid_from',
        'valid_to',
        'for_from',
        'for_to'
    ];

    //SCOPES

    /**
     * Return if a promocode is valid to use for a Pass
     *
     * @param Builder $query incoming query
     *
     * @return Builder
     */
    public function scopeIsValid(Builder $query) : Builder
    {
        return $query->notCanceled()
            ->singleUse()
            //->show($pass)
            ->validDates();
        //->forDates($pass);
    }

    /**
     * Return if a promocode is not Canceled
     *
     * @param Builder $query incoming query
     *
     * @return Builder
     */
    public function scopeNotCanceled(Builder $query) : Builder
    {
        return $query->where('canceled', 0);
    }

    /**
     * Return if a promocode is not for single use or has been applied on any reservations
     *
     * @param Builder $query incoming query
     *
     * @return Builder
     */
    public function scopeSingleUse(Builder $query) : Builder
    {
        return $query->where(
            function (Builder $query) {
                $query->where('single_use', 0)->orWhere(
                    function (Builder $query) {
                        $query->where('single_use', 1)->doesntHave('reservations');
                    }
                );
            }
        );
    }

    /**
     * Return if no has shows or if the show is valid on this promocode
     *
     * @param Builder $query incoming query
     * @param Pass    $pass  incoming pass
     *
     * @return Builder
     */
    public function scopeProduct(Builder $query, Pass $pass) : Builder
    {
        return $query->where(
            function (Builder $query) use ($pass) {
                $query->doesntHave('products')->orWhereHas(
                    'products', function (Builder $query) use ($pass) {
                        $query->where('id', $pass->product_id);
                    }
                );
            }
        );
    }

    /**
     * Return if today is a valid date to use the promocode
     *
     * @param Builder $query incoming query
     *
     * @return Builder
     */
    public function scopeValidDates(Builder $query) : Builder
    {
        return $query->where(
            function (Builder $query) {
                $query->where(
                    function (Builder $query) {
                        $query->whereNull('valid_from')->whereNull('valid_to');
                    }
                )->orWhere(
                    function (Builder $query) {
                        $query->whereNotNull('valid_from')->whereNotNull('valid_to');
                        $query->whereColumn(DB::raw('NOW()'), '>', 'valid_from');
                        $query->whereColumn(DB::raw('NOW()'), '<', 'valid_to');
                    }
                )->orWhere(
                    function (Builder $query) {
                        $query->whereNotNull('valid_from')->whereNull('valid_to');
                        $query->whereColumn(DB::raw('NOW()'), '>', 'valid_from');
                    }
                )->orWhere(
                    function (Builder $query) {
                        $query->whereNull('valid_from')->whereNotNull('valid_to');
                        $query->whereColumn(DB::raw('NOW()'), '<', 'valid_to');
                    }
                );
            }
        );
    }

    /**
     * Return if the date of the pass is valid to use this promocode
     *
     * @param Builder $query incoming query
     * @param Pass    $pass  incoming pass
     *
     * @return Builder
     */
    public function scopeForDates(Builder $query, Pass $pass) : Builder
    {
        return $query->where(
            function (Builder $query) use ($pass) {
                $query->where(
                    function (Builder $query) {
                        $query->whereNull('for_from')->whereNull('for_to');
                    }
                )->orWhere(
                    function (Builder $query) use ($pass) {
                        $query->whereNotNull('for_from')->whereNotNull('for_to');
                        $query->where('for_from', '<', $pass->datetime);
                        $query->where('for_to', '>', $pass->datetime);
                    }
                )->orWhere(
                    function (Builder $query) use ($pass) {
                        $query->whereNotNull('for_from')->whereNull('for_to');
                        $query->where('for_from', '<', $pass->datetime);
                    }
                )->orWhere(
                    function (Builder $query) use ($pass) {
                        $query->whereNull('for_from')->whereNotNull('for_to');
                        $query->where('for_to', '>', $pass->datetime);
                    }
                );
            }
        );
    }

    //RELATIONS
    public function products() : BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }

    public function reservations() : HasMany
    {
        return $this->hasMany(config('crs.reservations-model'));
    }

    public function packs() : BelongsToMany
    {
        return $this->belongsToMany(Pack::class, 'promocode_pack');
    }

    public function wristbands() : BelongsToMany
    {
        return $this->belongsToMany(Wristband::class);
    }

    public function setValidFromAttribute(String $value) : void
    {
        $this->attributes['valid_from'] = Carbon::parse($value)->format('Y-m-d 00:00');
    }

    public function setValidToAttribute(String $value) : void
    {
        $this->attributes['valid_to'] = Carbon::parse($value)->format('Y-m-d 00:00');
    }

    public function setForFromAttribute(String $value) : void
    {
        $this->attributes['for_from'] = Carbon::parse($value)->format('Y-m-d 00:00');
    }

    public function setForToAttribute(String $value) : void
    {
        $this->attributes['for_to'] = Carbon::parse($value)->format('Y-m-d 00:00');
    }

    public function calculateDiscountByPrice(String $price) : float
    {
        $discount = $this->discount / 100;
        return (float)($price * $discount);
    }
}
