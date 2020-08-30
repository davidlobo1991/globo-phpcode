<?php

namespace Globobalear\Products\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Globobalear\Promocodes\Models\Promocode;
use Globobalear\Wristband\Models\WristbandPass;
use Globobalear\Products\Models\Provider;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends BaseModel
{
    use HasTranslations;
    use SoftDeletes;

    public $translatable = ['description'];
    protected $fillable = ['name',
        'acronym',
        'description',
        'image',
        'sort',
        'category_id',
        'commission',
        'provider_id',
        'has_passes',
        'has_quota',
        'limit_hours',
        'limit_days',
    ];
    protected $hidden = ['created_at',
        'updated_at',
        'commission',
        'deleted_at',
        'image',
        'sort',
        'category_id',
        'provider_id',
    ];
    protected $casts = ['has_passes' => 'boolean',
        'has_quota' => 'boolean',
        'limit_hours' => 'int',
        'limit_days' => 'int',
    ];

    /** RELATIONS */
    public function passes()
    {
        return $this->hasMany(Pass::class);
    }

    public function ticketTypes()
    {
        return $this->belongsToMany(TicketType::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductsPrice::class);
    }

    public function seatTypes()
    {
        return $this->belongsToMany(SeatType::class)->withPivot('default_quantity');
    }

    public function category()
    {
        return $this->belongsTo(\Globobalear\Products\Models\Category::class);
    }

    public function provider() : BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function wristbandPasses():  BelongsToMany
    {
        return $this->belongsToMany(WristbandPass::class);
    }


    /**
     * Borrar despues de los tests
     * @deprecated
     * @param $query
     * @return mixed
     */
     public function _scopeGetNextPasses($query)
     {
         return $query->with(['passes' => function($q){
             $q->getNextPasses()->notCanceled()->onSale();
         }, 'seatTypes', 'category', 'ticketTypes']);
     }


    public function scopeGetNextPasses($query)
    {
        return $query->with(['passes' => function($q){
            $q->getNextPasses()->notCanceled()->onSale()
                ->with(['passes_seat_types' => function($qr){
                    $qr->active()->with(['seatType' => function($seat){
                    }]);
                }]);
        }]);
    }

    public function promocodes()
    {
        return $this->belongsToMany(Promocode::class, 'product_promocode')->isValid();
    }


}
