<?php

namespace Globobalear\Packs\Controllers;

use App\Helpers\Helper;
use Carbon\Carbon;
use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use DateInterval;
use DatePeriod;
use DateTime;
use App\Reservation;

use Globobalear\Packs\Models\Pack;
use Globobalear\Packs\Models\ViewPack;
use Globobalear\Packs\Models\PackProduct;
use Globobalear\Packs\Models\PackShowPirates;
use Globobalear\Packs\Models\PackWristband;

use Globobalear\Products\Models\Pass;
use Globobalear\Products\Models\Product;
use Globobalear\Products\Models\ShowPirates;
use Globobalear\Products\Models\SeatTypePirates;
use Globobalear\Products\Models\TicketTypePirates;
use Globobalear\Products\Models\SeatType;
use Globobalear\Products\Models\TicketType;

use Globobalear\Wristband\Models\Wristband;
use Globobalear\Wristband\Models\WristbandPass;

use App\Language;
use Illuminate\Http\JsonResponse;
use App\User;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Auth;

class PacksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (!Auth::user()->role->role_permission('view_pack')) {
            abort(403, 'Unauthorized');
        }

        return view('packs::packs.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $packs = Pack::all()->map->setAppends([
            'dateStartFormated',
            'dateEndFormated'
        ]);

        return $datatables->collection($packs)
            ->addColumn('action', 'packs::packs.actions')
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create(Request $request)
    {
        if (!Auth::user()->role->role_permission('create_pack')) {
            abort(403, 'Unauthorized');
        }

        $currentDate = Carbon::now();
        $dateStart = $currentDate->startOfDay();
        $startDate = $currentDate->format('d-m-Y');
        $tickettype = TicketType::pluck('title', 'id');
        $wristabandPass = WristbandPass::orderBy('title')->get();

        return view('packs::packs.create', compact('startDate', 'request', 'tickettype', 'wristabandPass'));
    }

    /**
     * Create a form list por generate events
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function generateList(Request $request)
    {

        $currentDate = Carbon::now();
        $dateStart = $currentDate->startOfDay();
        $startDate = $currentDate->format('d-m-Y');
        $products = Product::pluck('name', 'id')->prepend('- Available Products -', 0);
        $seatypes = [];

        $view = view('packs::packs.pack', [
            'el' => $request->element,
            'currentDate' => $currentDate,
            'dateStart' => $startDate,
            'products'=> $products,
            'seatypes'=> $seatypes
        ]);

        $view = $view->render();

        return [
            'view' => $view
        ];
    }


    /**
     * Create a form list por generate events
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function generateShowsList(Request $request)
    {
        $currentDate = Carbon::now();
        $dateStart = $currentDate->startOfDay();
        $startDate = $currentDate->format('d-m-Y');
        $shows = ShowPirates::pluck('title', 'id')->prepend('- Available Shows -', 0);
        $seatypes = SeatTypePirates::pluck('title', 'id');

        $view = view('packs::packs.showspack', [
            'elpirates' => $request->element,
            'currentDate' => $currentDate,
            'dateStart' => $startDate,
            'shows'=> $shows,
            'seatypes'=> $seatypes
        ]);

        $view = $view->render();

        return [
            'view' => $view
        ];
    }

    /**
     * Return list of all Passes by Show
     *
     * @return JsonResponse
     */
    public function listSeatTypes(Request $request): JsonResponse
    {
        $product = Product::with([
            'seatTypes' => function ($query) {
                $query->enable();
            },
            'ticketTypes'
        ])->find($request->productId);

        $seatTypes = $product->seatTypes;

        $ticketTypes = $product->ticketTypes;

        $packlines = collect();

        $view = view('packs::packs.seattypes', [
            'seatypes' => $seatTypes,
            'ticketTypes' => $ticketTypes,
            'packlines' => $packlines,
            'el' => $request->element
        ])->render();

        return response()->json([
            'html' => $view
        ]);
    }

    /**
     * Return list of all Passes by Show
     *
     * @return JsonResponse
     */
    public function listShowSeatTypes(Request $request): JsonResponse
    {
        $product = ShowPirates::with([
            'seatTypes',
            'ticketTypes'
        ])->find($request->showId);

        $seatTypes = $product->seatTypes;

        $ticketTypes = $product->ticketTypes;

        if ($ticketTypes->isEmpty()) {
            $ticketTypes = TicketTypePirates::all();
        }

        $packlinesPirates = collect();

        $view = view('packs::packs.showsseattypes', [
            'seatypes' => $seatTypes,
            'ticketTypes' => $ticketTypes,
            'packlinesPirates' => $packlinesPirates,
            'elpirates' => $request->element
        ])->render();

        return response()->json([
            'html' => $view
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        if (!Auth::user()->role->role_permission('create_pack')) {
            abort(403, 'Unauthorized');
        }

        $inputData = $request->all();

        $validation = $this->validate($request, [
            'title' => 'required',
            'acronym' => 'required',
            'date_start' => 'required|date',
            'date_end' => 'required|date',
        ]);

        // //add line products
        // $packsData = [];
        // if (isset($request->el)) {
        //     $this->validate($request, [
        //         "products" => "required|array",
        //         "products.*" => "required",
        //         "seatypes" => "required|array",
        //         "seatypes.*" => "required",
        //         "price" => "required|array",
        //         "price.*" => "required|numeric",
        //     ]);
        // }

        $packsData = $this->setPacksData($request);

        //add line shows
        // $packsPiratesData = [];
        // if (isset($request->elpirates)) {
        //     $this->validate($request, [
        //         "showspirates" => "required|array",
        //         "showspirates.*" => "required",
        //         "seatypespirates" => "required|array",
        //         "seatypespirates.*" => "required",
        //         "pricepirates" => "required|array",
        //         "pricepirates.*" => "required|numeric",
        //     ]);
        // }

        $packsPiratesData = $this->setPacksPiratesData($request);

        $pack = Pack::create($inputData);

        $pack->packline()->saveMany($packsData);
        $pack->packlinePirates()->saveMany($packsPiratesData);
/*        $pack->packlineWristband()->save($packsWristbandData);*/

        Toastr::success('Pack create successfully', 'Created!');

        return redirect()->route('packs.index');
    }


     /**
     * @param Request $request
     * @param $el
     * @param $pack
     */
    private function setPacksData(Request $request)
    {
        $packs = [];

        foreach ($request->products as $product) {
            if (!isset($product['seatTypes'])) {
                continue;
                // dd('if (!isset($product["seatTypes"])) {');
            }
            foreach ($product['seatTypes'] as $seatTypeId => $seatType) {
                if (!isset($seatType['ticketTypes'])) {
                    continue;
                    // dd('if (!isset($seatType["ticketTypes"])) {');
                }
                foreach ($seatType['ticketTypes'] as $ticketTypeId => $ticketType) {
                    $packs[] = new PackProduct([
                        "product_id" => $product['id'],
                        "seat_type_id" => $seatType['id'],
                        "ticket_type_id" => $ticketType['id'],
                        "price" => $ticketType['price']
                    ]);
                }
            }
        }

        return $packs;
    }



      /**
     * @param Request $request
     * @param $el
     * @param $pack
     */
    private function setPacksPiratesData(Request $request)
    {
        $packsPirates = [];

        if (isset($request->showspirates) && count($request->showspirates) > 0) {
            foreach ($request->showspirates as $show) {
                if (!isset($show['seatTypes'])) {
                    continue;
                    // dd('if (!isset($show["seatTypes"])) {');
                }
                foreach ($show['seatTypes'] as $seatTypeId => $seatType) {
                    if (!isset($seatType['ticketTypes'])) {
                        continue;
                        // dd('if (!isset($seatType["ticketTypes"])) {');
                    }
                    foreach ($seatType['ticketTypes'] as $ticketTypeId => $ticketType) {
                        $packsPirates[] = new PackShowPirates([
                            "show_id" => $show['id'],
                            "seat_type_id" => $seatType['id'],
                            "ticket_type_id" => $ticketType['id'],
                            "price" => $ticketType['price']
                        ]);
                    }
                }
            }
        }

        return $packsPirates;
    }


    /**
     * @param $request
     * @return PackWristband
     */
    private function setPacksWristbandData($request)
    {
        $wristband= WristbandPass::where('id', $request)->first();

        $packWristband = new PackWristband([
            "wristband_passes_id" => $wristband->id,
            "price" => $wristband->price
        ]);

        return $packWristband;
    }


    /**
     * @param Pack $pack
     */
    public function show(Pack $pack)
    {
        return redirect()->route('packs.edit', $pack->id);
    }

    /**
     * @param Pack $pack
     * @return \Illuminate\Contracts\View\Factory|View
     */
    public function edit(Pack $pack)
    {
        if (!Auth::user()->role->role_permission('edit_pack')) {
            abort(403, 'Unauthorized');
        }

        $currentDate = Carbon::now();
        $dateStart = $currentDate->startOfDay();
        $startDate = $currentDate->format('d-m-Y');

        $products = Product::pluck('name', 'id')->prepend('- Available Products -', 0);
        $shows = ShowPirates::pluck('title', 'id')->prepend('- Available Shows -', 0);

        $pack->load([
            'packline.product.seatTypes' => function ($query) {
                $query->enable();
            },
            'packline.product.ticketTypes',
            'packlinePirates.show.seatTypes',
            'packlinePirates.show.ticketTypes'
        ]);

        $allTicketTypesPirates = TicketTypePirates::all();

        return view('packs::packs.edit', compact('pack', 'startDate', 'products', 'shows', 'allTicketTypesPirates'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  SeatType $seatType
     * @return Redirect
     */
    public function update(Request $request, Pack $pack)
    {
        if (!Auth::user()->role->role_permission('edit_pack')) {
            abort(403, 'Unauthorized');
        }

        $inputData = $request->all();
        $this->validate($request, [
            'title' => 'required',
            'acronym' => 'required',
            'date_start' => 'required|date',
            'date_end' => 'required|date',
        ]);

        $pack->update($inputData);

        //add line products
        $this->savePacksData($request, $pack);

        //add line shows
        $this->savePacksPiratesData($request, $pack);

        //add line wristaband
        $this->savePacksWristbandData($request, $pack);


        Toastr::success('Pack updated successfully', 'Updated!');

        return redirect(route('packs.edit', $pack->id));
    }

     /**
     * @param Request $request
     * @param $el
     * @param $pack
     */
    private function savePacksData(Request $request, Pack $pack)
    {
        $packs = $this->setPacksData($request);

        $pack->packline()->delete();

        $pack->packline()->saveMany($packs);
    }

      /**
     * @param Request $request
     * @param $el
     * @param $pack
     */
    private function savePacksPiratesData(Request $request, Pack $pack)
    {
        $packsPirates = $this->setPacksPiratesData($request);

        $pack->packlinePirates()->delete();

        $pack->packlinePirates()->saveMany($packsPirates);
    }


    /**
     * @param $request
     * @param Pack $pack
     * @return bool
     */
    private function savePacksWristbandData($request, Pack $pack)
    {
        $pack->packlineWristband()->delete();

        $wristband = WristbandPass::where('id', $request->wristband)->first();
        if (!$wristband) {
            return false;
        }

        $packsWristband = new PackWristband([
            "wristband_passes_id" => $wristband->id,
            "price"   => $wristband->price
        ]);

        $pack->packlineWristband()->save($packsWristband);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  SeatType $seatType
     * @return Redirect
     */
    public function destroy(Pack $pack)
    {
        if (!Auth::user()->role->role_permission('delete_pack')) {
            abort(403, 'Unauthorized');
        }

        $pack->forceDelete();

        Toastr::success('Pack deleted successfully', 'Deleted!');

        return redirect()->route('packs.index');
    }


    /**
     * Generate table to select tickets on reservations view
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function tablePacks(Request $request)
    {
        $id = $request->pack;
        $pack = Pack::find($id);
        $packline = $pack->packline()->with(['passes' => function ($query) use ($pack) {
            $query->where('datetime', '>=', $pack->dateStartReservations);
            $query->where('datetime', '<=', $pack->DateEndReservations);
        }])->get();

        $packlinePirates = $pack->packlinePirates()->with(['passes' => function ($query) use ($pack) {
            $query->where('datetime', '>=', $pack->dateStartReservations);
            $query->where('datetime', '<=', $pack->DateEndReservations);
        }])->get();

        $packlineWristbands = $pack->packlineWristband()->with('wristbands')->first();

        if (empty($pack) && empty($packline)) {
            $view = '<h4 class="text-center">This pack has not Shows or Product assigned</h4>';
        } else {
            $reservation = Reservation::with('reservationPacks')->with('reservationPacksPirates')->find($request->reservation);

            $view = view('packs::partials.tablePacks', compact('pack', 'packline', 'packlinePirates', 'reservation', 'packlineWristbands'));
            $view = $view->render();
        }

        return [
            'view' => $view
        ];
    }
}
