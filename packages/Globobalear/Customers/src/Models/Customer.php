<?php

namespace Globobalear\Customers\Models;

use Carbon\Carbon;

use App\ViewReservation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Spatie\Translatable\HasTranslations;


class Customer extends Model
{
    use HasTranslations, SoftDeletes;

    public $fillable = [
        'name',
        'surname',
        'is_enabled',
        'newsletter',
        'resident',
        'customer_nationality_id',
        'customer_how_you_meet_us_id',
        'identification_number',
        'birth_date',
        'phone',
        'email',
        'internal_comments',
        'gender_id',
        'languages_id'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'newsletter' => 'boolean',
        'resident' => 'boolean'
    ];

    protected $dates = ['birth_date'];

    /**
     * Gender relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gender() : BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }

    /**
     * Customers language relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customersLanguage() : BelongsTo
    {
        return $this->belongsTo(CustomersLanguage::class);
    }

    /**
     * Reservations relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations() : HasMany
    {
        return $this->hasMany(ViewReservation::class);
    }
}
