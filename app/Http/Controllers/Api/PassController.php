<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Traits\PassFormat;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use Globobalear\Products\Models\Pass;

use Log;
use Exception;

class PassController extends Controller
{
    use PassFormat;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() : JsonResponse
    {
        $passes = $this->setFormat(Pass::get());

        return response()->json(['data' => $passes]);
    }

    /**
     * Display the specified resource.
     *
     * @param String $lang current lang
     * @param int    $id   the target model
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(String $lang, int $id) : JsonResponse
    {
        try {
            $pass = Pass::notCanceled()
                ->onSale()
                ->getNextPasses()
                ->findOrFail($id);

            $data = $this->setFormat([$pass], true);

            return response()->json(compact('data'));
        } catch (Exception $e) {
            Log::error($e);

            return response()->json(null);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param String $lang current lang
     * @param int    $id   the target model
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function availability(String $lang, int $id) : JsonResponse
    {
        try {

            $pass = DB::select('CALL sp_reservations_availability(?,?)', [$id, 1]);
            return response()->json(['data' => $pass]);

        } catch (\Exception $e) {
            Log::error($e);
            
            return  response()->json(null);
        }
    }

    /**
     * Filters passes by date
     * 
     * @param String $lang          current lang
     * @param String or null $month month to search
     * @param String or null $year  year to search
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function passesByDate(String $lang, ? String $month = null, ? String $year = null) : JsonResponse
    {
        if (is_null($month)) {
            $month = date('m');
        }

        if (is_null($year)) {
            $year = date('Y');
        }

        $dateInit = Carbon::createFromDate($year, $month, '01')->startOfMonth();
        $dateInit = $dateInit->startOfMonth();

        $dateEnd = Carbon::createFromDate($year, $month, '01');
        $dateEnd = $dateEnd->endOfMonth();

        $passes = Pass::notCanceled()->where('on_sale', 1)->orderBy('datetime');
        $passes = $passes->with('products')->get();

        $passes = $passes->whereBetween('datetime', [$dateInit, $dateEnd])->get();

        $data = $this->setFormat($passes);

        return response()->json(compact('data'));
    }

    /**
     * Get passes by product
     * 
     * @param String          $lang      current lang
     * @param Integer or null $productId id to take
     * @param Integer or null $take      taken quantity
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function passesByProduct(String $lang, ? int $productId = null, ? int $take = null) : JsonResponse
    {
        $passes = Pass::getNextPasses()
            ->notCanceled()
            ->onSale();

        if (!is_null($take)) {
            $passes->take($take);
        }

        if (!is_null($productId)) {
            $passes->whereHas(
                'product', function ($query) use ($productId) {
                    $query->where('id', $productId);
                }
            )->with('product');
        }

        $data = $this->setFormat($passes->get());

        return response()->json(compact('data'));
    }


    /**
     * All passes from a product between two dates
     * 
     * @param String          $lang          api language 'es'|'en'
     * @param String or null  $dateStartPack date 'Y-m-d'
     * @param String or null  $dateEndPack   date 'Y-m-d'
     * @param Integer or null $productId     show id
     * 
     * @return [type]
     */
    public function passesByProductAndDateRange(String $lang = 'es', ? String $dateStartPack = null, ? String $dateEndPack = null, ? int $productId = null) : JsonResponse
    {
        $dateInit = Carbon::createFromFormat("Y-m-d", $dateStartPack);
        if (is_null($dateEndPack)) {
            $dateEnd = $dateInit->copy()->addWeek();
        } else {
            $dateEnd = Carbon::createFromFormat("Y-m-d", $dateEndPack);
        }

        $passes = Pass::notCanceled()->where('on_sale', 1)->orderBy('datetime');
        if (!is_null($productId)) {
            $passes->whereHas(
                'product', function ($query) use ($productId) {
                    $query->where('id', $productId);
                }
            )->with('product');
        }

        $passes = $passes->whereBetween('datetime', [$dateInit, $dateEnd])->get();
        $data = $this->setFormat($passes);

        return response()->json(compact('data'));
    }
}
