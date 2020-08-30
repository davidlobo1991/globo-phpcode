<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Globobalear\Wristband\Models\Wristband;
use Globobalear\Wristband\Models\WristbandPass;

class WristbandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wristbands = Wristband::all(['id', 'title', 'acronym']);

        return response()->json(['data' => $wristbands]);
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
        $wristband = Wristband::where('id', $id)->get(['id', 'title', 'acronym'])->first();
        
        return response()->json(['data' => $wristband]);
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function passes($lang, $id)
    {
        $wristbandPasses = WristbandPass::select('wristband_passes.id', 'wristband_passes.title', 'date_start', 'date_end', 'price', 'quantity',
            \DB::raw('wristbands.id as wristband_id, wristbands.title as wristband'))
            ->leftJoin('wristbands', 'wristbands.id', 'wristband_passes.wristband_id')
            ->where('wristbands.id', $id)
            ->get();

        return response()->json(['data' => $wristbandPasses]);
    }
}
