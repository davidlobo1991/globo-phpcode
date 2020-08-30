<?php

namespace Globobalear\Payments\Traits;

use App\ReservationType;

use Illuminate\Http\Request;

use Globobalear\Packs\Models\Pack;

use Globobalear\Products\Models\Pass;

use Globobalear\Promocodes\Models\Promocode;

use Globobalear\Promocodes\Traits\PromocodeAction;

use Log;
use Exception;

trait PromocodeSecure
{
    use PromocodeAction;

    /**
     * Gets a verified promocode
     *
     * @param Request $request the request
     *
     * @return Promocode
     */
    public function getVerifiedPromocode($promocode, $reservationTypeId, $cartProduct) : ?Promocode
    {
        $promocodeToCheck = Promocode::with(['products', 'packs', 'wristbands'])
            ->whereCode($promocode)
            ->isValid()
            ->first();

        if (!$promocodeToCheck) {
            return null;
        }

        switch ($reservationTypeId) {
            case ReservationType::PRODUCTS:
                $promocode = $this->checkPromocodeForPass($cartProduct, $promocodeToCheck);
                break;
            case ReservationType::PACKS:
                $promocode = $this->checkPromocodeForPack($cartProduct, $promocodeToCheck);
                break;
        }

        return $promocode;
    }
}
