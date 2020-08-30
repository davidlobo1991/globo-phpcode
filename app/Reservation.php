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
use Globobalear\Packs\Traits\HasPack;
use Globobalear\Transport\Traits\HasTransport;
use Globobalear\Menus\Traits\HasMenu;
use App\Http\Controllers\Traits\HasEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Globobalear\Menus\Models\ReservationMenu;
use Globobalear\Customers\Models\Customer;
use Globobalear\Products\Models\Product;
use Globobalear\Products\Models\Pass;
use Globobalear\Packs\Models\Pack;
use Globobalear\Promocodes\Models\Promocode;
use Globobalear\Payments\Models\PaymentMethod;
use App\ReservationPack;
use Globobalear\Transport\Models\ReservationTransport;
use Globobalear\Wristband\Models\WristbandPass;
use Globobalear\Wristband\Traits\HasPaymentWristbandPass;
use App\ReservationPackPirates;


class Reservation extends Model
{
    use HasEmail,HasPass, HasPromocodes, HasTransport, HasPayments,HasTransport,HasPack, HasPaymentWristbandPass, SoftDeletes;

    protected $fillable = [
        'product_id',
        'pass_id',
        'pack_id',
        'email',
        'name',
        'surname',
        'reservation_number',
        'reference_number',
        'discount',
        'promocode_id',
        'channel_id','customer_id',
        'reseller_id',
        'phone',
        'identification_number',
        'created_by',
        'canceled_by',
        'finished',
        'comments',
        'booking_fee',
        'paypal',
    ];

    protected $appends = [
        'totalPrice',
        'pendingToPay',
        'pendingPacktoPay',
        'HasEmail'
    ];

    protected $dates = ['created_at', 'canceled_date'];

    /** ACCESSORS & MUTATORS */

    //PRODUCT PRICE FINAL
    public function getTotalPriceAttribute(): float
    {
        $total = 0;
        $total += $this->booking_fee;
        $total += $this->getTicketPriceAttribute();
        $total += $this->getTicketPackPriceAttribute();
        $total += $this->getTransportPriceAttribute();
        $promocode = $this->getPromocodeAttribute($total);
        $total = $total - $promocode;
        $paypal = $this->tax($total);
        $total = $total + $paypal;

        return $total;
    }

    //PACK PRICE FINAL
    public function getTotalPackPriceAttribute(): float
    {
        $total = 0;
        $total += $this->booking_fee;
        $total += $this->getTicketPackPriceAttribute();
        $total += $this->getTransportPriceAttribute();
        $promocode = $this->getPromocodeAttribute($total);
        $total = $total - $promocode;
       // dd($total);
        $paypal = $this->tax($total);
        $total = $total + $paypal;

        return $total;
    }


    //WRISTBAND PRICE FINAL
    public function getTotalWristbandsPriceAttribute(): float
    {
        $total = 0;
        $total += $this->booking_fee;
        $total += $this->getTotalPriceTicketWristbandPassAttribute();
        $total += $this->getTransportPriceAttribute();
        $promocode = $this->getPromocodeAttribute($total);
        $total = $total - $promocode;
        $paypal = $this->tax($total);
        $total = $total + $paypal;

        return $total;
    }

    public function tax($total): float
     {
        if($this->paypal > 0){
            return $total * $this->paypal /100;
        }
        else{

            return  $total = 0;
        }

     }

    /**
     *
     */
    public function getNameReservationAttribute()
    {
        if ($this->pack_id) {
            return $this->pack;
        }

        if ($this->pass_id) {
            return $this->pass->product->name . ' | ' . $this->pass->datetime;
        } else {
            return $this->product->name;
        }

        return "";
    }

    /**
     * @return int
     */
     public function getTotalPaid()
     {
         $payments = $this->load('payment_methods')->payment_methods;

         $total = 0;
         foreach ($payments as $payment) {
             $total += $payment->pivot->total;
         }

         return $total;
     }

    /**
     * @return int
     */
    public function remainderToPay()
    {
        $this->load('payment_methods');

        $totalPrice = round($this->getTotalPriceAttribute(), 2);
        $totalPaid = round($this->getTotalPaid(), 2);

        $remainderToPay = $totalPrice - $totalPaid;

        return number_format($remainderToPay, 2, '.', '');
    }


    public function remainderToPayPack()
    {
        $this->load('payment_methods');

        $totalPrice = round($this->getTotalPackPriceAttribute(), 2);
        $totalPaid = round($this->getTotalPaid(), 2);

        $remainderToPay = $totalPrice - $totalPaid;

        return number_format($remainderToPay, 2, '.', '');
    }

    public function remainderToPayWristband()
    {
        $this->load('payment_methods');

        $totalPrice = round($this->getTotalWristbandsPriceAttribute(), 2);
        $totalPaid = round($this->getTotalPaid(), 2);

        $remainderToPay = $totalPrice - $totalPaid;

        return number_format($remainderToPay, 2, '.', '');
    }


     public function remainderToPayInteger()
     {
         $this->load('payment_methods');
         $totalPrice = round($this->getTotalPriceAttribute(), 2);
         $totalPaid = round($this->getTotalPaid(), 2);

         $remainderToPay = $totalPrice - $totalPaid;

         if($remainderToPay == 0.0) {
             $paid= true;
         }
        else{
            $paid= false;
        }
         return $paid;
     }


    public function remainderToPayPackInteger()
    {
        $this->load('payment_methods');
        $totalPrice = round($this->getTotalPackPriceAttribute(), 2);
        $totalPaid = round($this->getTotalPaid(), 2);

        $remainderToPay = $totalPrice - $totalPaid;

        if($remainderToPay == 0.0) {
            $paid= true;
        }
       else{
           $paid= false;
       }


        return $paid;
    }

    public function remainderToPayWristbandInteger()
    {
        $this->load('payment_methods');
        $totalPrice = round($this->getTotalWristbandsPriceAttribute(), 2);
        $totalPaid = round($this->getTotalPaid(), 2);

        $remainderToPay = $totalPrice - $totalPaid;

        if($remainderToPay == 0.0) {
            $paid= true;
        }
       else{
           $paid= false;
       }
        return $paid;
    }



    /** RELATIONS */

    public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
    }

    public function created_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function canceled_by_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'canceled_by');
    }


    public function reservationTickets(): HasMany
    {
        return $this->hasMany(ReservationTicket::class);
    }

    public function reservationPacks(): HasMany
    {
        return $this->hasMany(ReservationPack::class);
    }

    public function ReservationPacksPirates(): HasMany
    {
        return $this->hasMany(ReservationPackPirates::class);
    }

    public function reservationPacksWristband(): HasOne
    {
        return $this->hasOne(ReservationPackWristband::class);
    }

    public function reservationWristbandPasses(): belongsToMany
    {
        return $this->belongsToMany(WristbandPass::class)->withPivot('quantity')->withTimestamps();
    }

    public function ReservationTransport(): HasMany
    {
        return $this->hasMany(ReservationTransport::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function reservationMenu(): HasMany
    {
        return $this->hasMany(ReservationMenu::class);
    }

    public function customers()
    {
        return $this->hasOne(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function pass()
    {
        return ($this->belongsTo(Pass::class));
    }

    public function pack()
    {
        return ($this->belongsTo(Pack::class));
    }

    public function promocodes()
    {
        return $this->hasOne(Promocode::class, 'id', 'promocode_id');
    }

    public function payment_methods()
    {
        return $this->belongsToMany(PaymentMethod::Class, 'payment_method_reservations')
            ->withPivot('total', 'user_id','id')->withTimestamps()->whereNull('payment_method_reservations.deleted_at');
    }

    public function finishedBy()
    {
        return $this->belongsTo(User::class, 'finished_by', 'id');
    }

    public function getReservationTypeIdAttribute()
    {
        $variable = $this->attributes['reservation_type_id'];

        switch ($variable) {
            case ReservationType::PRODUCTS:
                $variable = ReservationType::PRODUCTS_ROUTE;
                break;
            case ReservationType::PACKS:
                $variable = ReservationType::PACKS_ROUTE;
                break;
            case ReservationType::WRISTBANDS:
                $variable = ReservationType::WRISTBANDS_ROUTE;
                break;


            default:
                $variable = ReservationType::ERROR_ROUTE;
                break;
        }
        return $variable;
    }

    public function getProviders()
    {
        if ($this->pack_id) {
            return $this->pack->packline
                ->map->products
                ->map->provider
                ->filter();
        }
        if ($this->pass->product->provider) {
            return collect([$this->pass->product->provider]);
        }
        return collect();
    }
}
