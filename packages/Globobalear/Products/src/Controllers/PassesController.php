<?php

namespace Globobalear\Products\Controllers;


use App\Helpers\Helper;
use App\Http\Notification\Facade\Toastr;
use App\Reservation;
use App\GlobalConf;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Products\Models\Pass;
use Globobalear\Products\Models\Product;
use Yajra\Datatables\Datatables;
use Auth;

class PassesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if(!Auth::user()->role->role_permission('view_passes'))
            abort(403, 'Unauthorized');

        return view('products::passes.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables): JsonResponse
    {
        $query = Pass::select('passes.id as id', 'datetime',  'products.name as name','on_sale',
        DB::raw('count(reservations.id) as total_reservations, concat(products.name," | " , date_format(datetime, "%d/%m/%Y %H:%i")) as pass, date_format(datetime, "%d/%m/%Y %H:%i")as fecha'))
            ->leftJoin('products', 'products.id', '=', 'passes.product_id')
            ->leftJoin('reservations', 'passes.id', '=', 'reservations.pass_id')
            ->groupBy('passes.id')->orderBy('datetime','desc')->get();



        return $datatables->collection($query)
            ->addColumn('action', 'products::passes.actions')
            /*->editColumn('test', function ($q) {
                return $q->datetime ? with(new Carbon($q->datetime))->format('d/m/Y H:i') : '';
            })
            ->orderColumn('test', 'datetime $1')*/
            ->editColumn('on_sale', function ($q) {
                return $q->on_sale ? 'Yes' : 'No';
            })
            ->setRowClass(function ($q) {
                return !$q->on_sale ? 'canceled' : '';
            })

            ->orderColumn('datetime', 'name $1')
            ->rawColumns([0])
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        if(!Auth::user()->role->role_permission('create_passes'))
            abort(403, 'Unauthorized');

        return view('products::passes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        if(!Auth::user()->role->role_permission('create_passes'))
            abort(403, 'Unauthorized');

        $this->validate($request, [
            "from" => "required|array",
            "from.*" => "required",
            "to" => "required|array",
            "to.*" => "required",
            "product" => "required|array",
            "product.*" => "required",
            "days" => "required|array",
            "days.*" => "required",
            "seats" => "required|array",
            "seats.*" => "required|min:1'",
            "prices" => "required|array",
            "prices.*" => "required",

        ]);



        if(!isset($request->seats) && $request->product[1]==0){
            Toastr::error('Error on generat passes, seat types in empty');

            return back();
        }


        $count = 0;

        foreach ($request->el as $el) {
            $days = $this->getDaysToGenerate($request->from[$el], $request->to[$el], $request->days[$el]);
            $time = explode(':', $request->hour[$el]);

            foreach ($days as $day) {
                $day->hour = $time[0];
                $day->minute = $time[1];

                $pass = Pass::create([
                    'datetime' => $day,
                    'product_id' => (int)$request->product[$el],
                ]);

                $count++;

                $this->setSeatsData($request, $el, $pass);
            }
        }

        $this->setToastrStore($count);

        return redirect()->route('passes.create');
    }

    /**
     * Display the specified resource.
     *
     * @param Pass $pass
     * @return Redirect
     */
    public function product(Pass $pass)
    {
        if(!Auth::user()->role->role_permission('show_passes'))
            abort(403, 'Unauthorized');

        return redirect()->route('products::passes.edit', $pass->id);
    }

    /**
     * Pass the form for editing the specified resource.
     *
     * @param  Pass $pass
     * @return View
     */
    public function edit(Pass $pass)
    {
        if(!Auth::user()->role->role_permission('edit_passes'))
            abort(403, 'Unauthorized');

        $pass->load('seatTypes', 'product.ticketTypes');

        return view('products::passes.edit')
            ->with('pass', $pass);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Redirect
     * @internal param Pass $pass
     */
    public function update(Pass $pass, Request $request)
    {
        if(!Auth::user()->role->role_permission('edit_passes'))
            abort(403, 'Unauthorized');

        foreach ($request->data as $seatTypeId => $dataTickets) {
            $passSeatType = DB::table('pass_seat_type')->where('pass_id', $request->pass_id)->where('seat_type_id', $seatTypeId);
            $passSeatTypeId = $passSeatType->first()->id;
            $passSeatType->update(
                [
                    'seats_available' => $dataTickets['qty'],
                    'web_available' => ((bool) isset($dataTickets["web_available"])) ?? false
                ]
            );

            foreach ($dataTickets['ticketTypes'] as $ticketTypeId => $price) {
                DB::table('passes_prices')->where('pass_seat_type_id', $passSeatTypeId)->where('ticket_type_id', $ticketTypeId)
                    ->update(['price' => $price]);
            }
        }

        $pass->save();

        Toastr::success('Pass updated successfully', 'Updated!');

        return redirect()->route('passes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Pass $pass
     * @return Redirect
     */
    public function destroy(Pass $pass)
    {
        if(!Auth::user()->role->role_permission('delete_passes'))
            abort(403, 'Unauthorized');

        $reservationsQty = $pass->load('reservations')->reservations->count();

        if ($reservationsQty) {
            Toastr::error('There are reservations for this pass', 'Error!');

            return redirect()->route('passes.index');
        }

        $pass->on_sale = !$pass->on_sale;
        $pass->save();

        if (!$pass->on_sale) {
            Toastr::success('Pass canceled successfully', 'Canceled!');
        } else {
            Toastr::success('Pass activated successfully', 'Activated!');
        }

        return redirect()->route('passes.index');
    }


    /************* AJAX METHODS *************/

    /**
     * Return list of all Passes by Show
     *
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $passes = Pass::where('product_id', $request->q)->where('on_sale',1)->orderBy('datetime');



        if (isset($request->s) && !is_null($request->s)) {
            $passes->where('datetime', 'like', "%{$request->s}%");
        }

        $passes = $passes->get();

        return response()->json($passes);
    }

    /**
     * Create a form list por generate events
     *
     * @param Request $request
     * @return View
     */
    public function generateList(Request $request)
    {
        $products = Product::pluck('name', 'id')->prepend('- Available Products -', 0);
        $days = Helper::getDaysArray();

        $view = view('products::partials.generateList',
            ['el' => $request->element, 'products' => $products, 'days' => $days]);

        $view = $view->render();

        echo $view;
    }

    /**
     * Create the table for passes generator
     *
     * @param Request $request
     * @return View
     */
    public function tableProduct(Request $request)
    {
        $pass = Product::with('seatTypes', 'ticketTypes')->find($request->product);

        if ($pass) {
            $seatTypes = $pass->seatTypes()->enable()->get();
            $ticketTypes = $pass->ticketTypes;
        } else {

            $seatTypes = collect();
            $ticketTypes = collect();
        }

        if ($seatTypes->isEmpty() || $ticketTypes->isEmpty()) {
            $view = '<h4 class="text-center">This product has not Seat or Ticket types assigned</h4>';
        } else {
            $view = view('products::partials.generateTable', [
                'el' => $request->el, 'ticketTypes' => $ticketTypes, 'seatTypes' => $seatTypes]);

            $view = $view->render();
        }

        echo $view;
    }

    /**
     * Generate table to select tickets on reservations view
     *
     * @param Request $request
     */
    public function tableTickets(Request $request)
    {
        $pass = Pass::with('seatTypes', 'product.ticketTypes')->find($request->pass);
        $seatTypes = $pass->seatTypes;

        $ticketTypes = $pass->product->ticketTypes;
        $global = GlobalConf::first();
        $product = $pass->product;

        if ($seatTypes->isEmpty() || $ticketTypes->isEmpty()) {
            $view = '<h4 class="text-center">This pass has not Seat or Ticket types assigned</h4>';
        } else {
            $reservation = Reservation::with('reservationTickets')->find($request->reservation);

            $view = view('products::partials.tableTicket',
                compact('seatTypes', 'ticketTypes', 'reservation', 'product', 'global'));

            try {
                $view = $view->render();
            } catch (\Exception $e) {
                $test = $e;
            }
        }
        echo $view;
    }


    /****************** PRIVATE METHODS *****************/

    /**
     * Return array of Carbons with the days selected between two dates
     *
     * @param string $dateStart
     * @param string $dateEnd
     * @param array $days
     * @return array
     */
    private function getDaysToGenerate(string $dateStart, string $dateEnd, array $days): array
    {
        $dateStart = Carbon::createFromFormat('d/m/Y', $dateStart)->format('y-m-d');
        $dateEnd = Carbon::createFromFormat('d/m/Y', $dateEnd)->format('y-m-d');

        $period = new DatePeriod(new DateTime($dateStart), new DateInterval('P1D'), new DateTime($dateEnd));

        $dates = [];
        foreach ($period as $day) {
            if (in_array($day->format('N'), $days)) {
                $dates[] = Carbon::parse($day->format('Y-m-d H:i:s'));
            }
        }

        return $dates;
    }

    /**
     * @param Request $request
     * @param $el
     * @param $pass
     */
    private function setSeatsData(Request $request, $el, $pass)
    {
        foreach ($request->seats[$el] as $seat => $value) {
            $passSeatType = DB::table('pass_seat_type')->insertGetId(
                [
                    'pass_id' => $pass->id,
                    'seat_type_id' => $seat,
                    'seats_available' => $value,
                ]
            );

            foreach ($request->prices[$el][$seat] as $ticket => $price) {
                DB::table('passes_prices')->insert(
                    [
                        'pass_seat_type_id' => $passSeatType,
                        'ticket_type_id' => $ticket,
                        'price' => $price
                    ]
                );
            }
        }
    }

    /**
     * @param $count
     */
    private function setToastrStore($count)
    {
        if ($count > 1) {
            Toastr::success($count . ' passes generated successfully');
        } else {
            Toastr::success($count . ' pass generated successfully');
        }
    }
}
