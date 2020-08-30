<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Globobalear\Wristband\Models\Wristband;
use Globobalear\Wristband\Models\WristbandPass;
use Illuminate\Support\Facades\DB;

class WristbandPassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wristbandPass = WristbandPass::select('wristband_passes.id', 'wristband_passes.title', 'date_start', 'date_end', 'price', 'quantity',
            \DB::raw('wristbands.id as wristband_id, wristbands.title as wristband'))
            ->leftJoin('wristbands', 'wristbands.id', 'wristband_passes.wristband_id')
            ->get();

        return response()->json(['data' => $wristbandPass]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($lang, $id)
    {
        $wristbandPass = Wristband::with('wristbandPasses')->find($id);

        return response()->json(['data' => $wristbandPass->wristbandPasses ?? [] ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function availability($lang, $id)
    {
        $wristbandPack = collect(DB::select( DB::raw("SELECT sum(quantity) as quantity FROM reservations inner join `reservation_pack_wristbands` on `reservation_pack_wristbands`.`reservation_id` = reservations.id where reservations.finished = 1 AND reservations.canceled_date IS NULL and reservations.deleted_at IS NULL and wristband_passes_id = $id")))->first();
        $wristband = collect(DB::select( DB::raw("SELECT sum(quantity) as quantity FROM reservations inner join `reservation_wristband_pass` on `reservation_wristband_pass`.`reservation_id` = reservations.id where reservations.finished = 1 AND reservations.canceled_date IS NULL and reservations.deleted_at IS NULL and reservation_wristband_pass.wristband_pass_id = $id ")))->first();
        $default_quantity = collect(DB::select( DB::raw("SELECT sum(quantity) as quantity FROM `wristband_passes`where wristband_id = $id")))->first();
        $result[] = ['wristband_sold' =>  $wristbandPack->quantity + $wristband->quantity,'default_quantity' =>  $default_quantity];
       
        return response()->json(['data' => $result ?? [] ]);
    }

/*
    DB::select( DB::raw("select sum(reservation_pack_wristbands.quantity) AS quantity
        from reservations inner join reservation_pack_wristbands on reservations.id = reservation_pack_wristbands.reservation_id
        where reservations.finished = 1 AND reservations.canceled_date IS NULL and reservations.deleted_at IS NULL
        GROUP by reservation_pack_wristbands.quantity"))*/


    /**
     * @param $lang
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function pass($lang, $id)
    {
        $wristbandPass = WristbandPass::where('id', $id)->with('wristband')->first();

        return response()->json(['data' => $wristbandPass]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
