<?php
/**
 * Created by PhpStorm.
 * User: moussa
 * Date: 30/11/17
 * Time: 13:10
 */

namespace Globobalear\Promocodes\Traits;

use Carbon\Carbon;

use Illuminate\Http\Request;

use Globobalear\Packs\Models\Pack;

use Globobalear\Promocodes\Models\Promocode;

use Globobalear\Products\Models\Pass;
use Globobalear\Products\Models\Product;

use Globobalear\Wristband\Models\WristbandPass;

trait PromocodeAction
{
    /**
     * Checks promocode for wristbands
     *
     * @param Promocode     $promocode       promocode to check
     * @param WristbandPass $wristbandPasses wristband passes to check
     *
     * @return Promocode
     */
    /*public function checkPromocodeForWristbands(Promocode $promocode, WristbandPass $wristbandPasses) : Promocode
    {
        if (!isset($promocode->wristbands)) {
            return new Promocode;
        }

        $wristbandPassesAvaible = [];
        foreach ($promocode->wristbands as $key => $wristband) {
            foreach ($wristband->wristbandPasses as $wristbandPass) {
                if (Carbon::parse($wristbandPass->date_start)->format('Y-m-d') >= Carbon::parse($promocode->for_from)->format('Y-m-d')) {
                    if (Carbon::parse($wristbandPass->date_end)->format('Y-m-d') <= Carbon::parse($promocode->for_to)->format('Y-m-d')) {
                        $wristbandPassesAvaible[] = $wristbandPass->id;
                    }
                }
            }
        }

        $matchPasses = array_intersect($wristbandPasses, $wristbandPassesAvaible);

        if (count($matchPasses) > 0) {
            return $promocode;
        }

        return new Promocode;
    }*/

    /**
     * Checks the promocode for the pass
     *
     * @param Pass      $pass      the   pass to check
     * @param Promocode $promocode promocode to check
     *
     * @return Promocode
     */
    public function checkPromocodeForPass($pass, Promocode $promocode) : ?Promocode
    {
        $passObject = Pass::find((int) $pass->passId);

        /*
         * Validacmos que el promocode este relacionado con el pack seleccinoado
         */
        if (!$passObject->product->promocodes->pluck('id')->contains($promocode->id)) {
            return null;
        }

        /*
         * Comprobamos que el dia de hoy esta en el rango de disponibilidad del promocode
         */
        $today = Carbon::today();
        if ($today->lt($promocode->valid_from) || $today->gt($promocode->valid_to)) {
            return null;
        }

        if ($pass->passDate->lt($promocode->for_from) || $pass->passDate->gt($promocode->for_to)) {
            return null;
        }

        return $promocode;
    }

    /**
     * Checks the promocode for pack
     *
     * @param Pack      $pack      pack to check
     * @param Promocode $promocode promocode to check
     *
     * @return Promocode
     */
    public function checkPromocodeForPack($pack, Promocode $promocode) : ?Promocode
    {
        $packObject = Pack::find($pack->packId);

        /*
         * Validacmos que el promocode este relacionado con el pack seleccinoado
         */
        if (!$packObject->promocodes->pluck('id')->contains($promocode->id)) {
            return null;
        }

        /*
         * Comprobamos que el dia de hoy esta en el rango de disponibilidad del promocode
         */
        $today = Carbon::today();
        if ($today->lt($promocode->valid_from) || $today->gt($promocode->valid_to)) {
            return null;
        }

        /*
         * Comprobamos que los productos que contienen el pase guardado esta entre las fechas del promocode
         */
        foreach ($pack->products as $product) {
            if ($product->passDate->lt($promocode->for_from) || $product->passDate->gt($promocode->for_to)) {
                return null;
            }
        }
        foreach ($pack->shows as $show) {
            if ($show->passDate->lt($promocode->for_from) || $show->passDate->gt($promocode->for_to)) {
                return null;
            }
        }

        return $promocode;
    }

    /**
     * Catch ID's of wristband-passes in request array
     *
     * @param Request $request the request
     *
     * @return array
     */
    // private function extractWristbandPasses(Request $request) : array
    // {
    //     $wristbandPasses = [];
    //     foreach ($request->wristbands as $wristband) {
    //         $wristbandPasses[] = $wristband['wristband_pass_id'];
    //     }

    //     return $wristbandPasses;
    // }
}
