<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Globobalear\Packs\Models\Pack;

class PackController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packs = Pack::all(); //with('packlinePirates')->with('packline')->get();
        $data = $this->setFormat($packs);

        return response()->json(['data' => $data]);
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
        $data = null;
        try {
            $packs = Pack::findOrFail($id);
            $data = $this->setFormat([$packs], true);
        } catch (\Exception $ex) {
            return response(['data' => null], 404);
        }

        return response(['data' => $data]);
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

    private function setFormat($packs, $product = false)
    {
        $data = [];
        foreach ($packs as $key => $pack) {
            $ticketTypes = $pack->packline->map->ticketType->unique();

            if ($ticketTypes->isEmpty()) {
                $ticketTypes = $pack->packlinePirates->map->ticketType->unique();
            }

            $data[$key] = [
                'id' => (int)$pack->id,
                'title' => $pack->title,
                'acronym' => $pack->acronym,
                'start' => $pack->date_start->format('d-m-Y'),
                'end' => $pack->date_end->format('d-m-Y'),
                'product' => $this->setFormatToProducts($pack->packline),
                'shows_pirates' => $this->setFormatToShows($pack->packlinePirates),
                'ticket_types' => $ticketTypes
                // 'wristband_pass' => [
                //     'data' => $pack->packlineWristband
                // ]
            ];
        }

        if ($product) {
            return $data[0];
        }

        return $data;
    }

    /**
     * @param $shows
     * @return array
     */
    private function setFormatToProducts($packlines)
    {
        $data = [];
        foreach ($packlines->groupBy('product_id') as $packlinesByProduct) {
            $packline = $packlinesByProduct->first();
            $product = $packline->product;

            $seatTypes = [];
            foreach ($packlinesByProduct->groupBy('seat_type_id') as $packlinesBySeatType) {
                $seatType = $packlinesBySeatType->first()->seatType;

                $ticketTypes = [];
                foreach ($packlinesBySeatType as $packlinesByTicketType) {
                    $ticketType = $packlinesByTicketType->ticketType;

                    // $ticketTypes[] = [
                    $ticketTypes[$ticketType->id] = [
                        'id' => $ticketType->id,
                        'title' => $ticketType->title,
                        'price' => $packlinesByTicketType->price
                    ];
                }

                // $seatTypes[] = [
                $seatTypes[$seatType->id] = [
                    'id' => $seatType->id,
                    'title' => $seatType->title,
                    'ticketTypes' => $ticketTypes
                ];
            }
            // $data[] = [
            $data[$product->id] = [
                'id' => $product->id,
                'pack_id' => $packline->pack_id,
                'title' => $product->name ??  $product->title,
                'has_passes' => $product->has_passes,
                'description' => $product->description ?? $product->{'description_'.\App::getLocale()},
                'seatTypes' => $seatTypes
            ];
        }

        return $data;
    }

    /**
     * @param $shows
     * @return array
     */
    private function setFormatToShows($packlinesPirates)
    {
        $data = [];

        foreach ($packlinesPirates->groupBy('product_id') as $packlinesPiratesByShow) {
            $packlinePirates = $packlinesPiratesByShow->first();
            $show = $packlinePirates->show;

            $seatTypes = [];
            foreach ($packlinesPiratesByShow->groupBy('seat_type_id') as $packlinesPiratesBySeatType) {
                $seatType = $packlinesPiratesBySeatType->first()->seatType;

                $ticketTypes = [];
                foreach ($packlinesPiratesBySeatType as $packlinesPiratesByTicketType) {
                    $ticketType = $packlinesPiratesByTicketType->ticketType;

                    // $ticketTypes[] = [
                    $ticketTypes[$ticketType->id] = [
                        'id' => $ticketType->id,
                        'title' => $ticketType->title,
                        'price' => $packlinesPiratesByTicketType->price
                    ];
                }

                // $seatTypes[] = [
                $seatTypes[$seatType->id] = [
                    'id' => $seatType->id,
                    'title' => $seatType->title,
                    'ticketTypes' => $ticketTypes
                ];
            }
            // $data[] = [
            $data[$show->id] = [
                'id' => $show->id,
                'pack_id' => $packlinePirates->pack_id,
                'title' => $show->name ??  $show->title,
                'has_passes' => true,
                'description' => $show->description ?? $show->{'description_'.\App::getLocale()},
                'seatTypes' => $seatTypes
            ];
        }

        return $data;
    }
}
