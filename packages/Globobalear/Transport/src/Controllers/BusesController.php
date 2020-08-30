<?php

namespace Globobalear\Transport\Controllers;


use App\Http\Notification\Facade\Toastr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Products\Models\Pass;
use Globobalear\Transport\Models\Bus;
use Globobalear\Transport\Models\Route;
use Globobalear\Transport\Models\Transporter;
use Yajra\Datatables\Datatables;

class BusesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        return view('transport::buses.index');
    }

    /**
     * @param Datatables $datatables
     * @return JsonResponse
     * @throws \Exception
     */
    public function data(Datatables $datatables)
    {
        $query = Bus::select('buses.id', 'capacity', 'routes.name as route',
            'transporters.name as transporter', 'products.name as product', 'passes.datetime')
            ->join('routes', 'routes.id', '=', 'buses.route_id')
            ->join('passes', 'passes.id', '=', 'buses.pass_id')
            ->join('products', 'products.id', '=', 'passes.product_id')
            ->leftJoin('transporters', 'transporters.id', '=', 'buses.transporter_id');

        return $datatables->eloquent($query)
            ->editColumn('datetime', function ($q) {
                return Carbon::parse($q->datetime)->format('d/m/Y');
            })
            ->orderColumn('datetime', 'passes.datetime $1')
            ->addColumn('action', 'transport::buses.actions')
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        $passes = Pass::where('on_sale', 1)->orderBy('datetime')->get()
            ->pluck('title', 'id');

        $transporters = Transporter::pluck('name', 'id');
        $routes = Route::pluck('name', 'id');

        return view('transport::buses.create')
            ->with('transporters', $transporters)
            ->with('routes', $routes)
            ->with('passes', $passes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'capacity' => 'required|numeric',
            'transporter_id' => 'required',
            'route_id' => 'required',
            'pass_id' => 'required',
        ]);

        foreach (collect($request->pass_id)->unique() as $passId) {
            Bus::create([
                'capacity' => $request->capacity,
                'transporter_id' => $request->transporter_id,
                'route_id' => $request->route_id,
                'pass_id' => $passId
            ]);
        }

        Toastr::success('Bus create successfully', 'Created!');

        return redirect()->route('buses.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Bus $bus
     * @return Redirect
     */
    public function show(Bus $bus)
    {
        return redirect()->route('transport::buses.edit', $bus->id);
    }

    /**
     * Bus the form for editing the specified resource.
     *
     * @param  Bus $bus
     * @return View
     */
    public function edit(Bus $bus)
    {
        $passes = Pass::where('on_sale', 1)->orderBy('datetime')->get()
            ->pluck('title', 'id');

        $transporters = Transporter::pluck('name', 'id');
        $routes = Route::pluck('name', 'id');

        return view('transport::buses.edit')
            ->with('transporters', $transporters)
            ->with('routes', $routes)
            ->with('passes', $passes)
            ->with('bus', $bus);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Bus $bus
     * @return Redirect
     */
    public function update(Request $request, Bus $bus)
    {
        $this->validate($request, [
            'capacity' => 'required|numeric',
            'transporter_id' => 'required',
            'route_id' => 'required',
            'pass_id.*' => 'required',
        ]);

        $bus->update($request->all());

        Toastr::success('Bus updated successfully', 'Updated!');

        return redirect()->route('buses.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Bus $bus
     * @return Redirect
     */
    public function destroy(Bus $bus)
    {
        $bus->delete();

        Toastr::success('Bus deleted successfully', 'Deleted!');

        return redirect()->route('buses.index');
    }


    /****************** AJAX METHODS ********************/

    /**
     * Return list of all Buses by Pass
     *
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $bus = Bus::where('pass_id', $request->q)
            ->with('transporter', 'route', 'pass.product')
            ->orderBy('transporter_id');

        $bus = $bus->get();

        return response()->json($bus);
    }

    /**
     * Return list of all PickupPoints by Bus
     *
     * @return JsonResponse
     */
    public function listPickupPoints(Request $request): JsonResponse
    {
        $bus = Bus::with('route.pickupPoints')->find($request->q);

        return response()->json($bus->route->pickupPoints);
    }

    /**
     * Generate ajax line for a bus on reservations CRUD
     *
     * @param Request $request
     * @return string
     */
    public function addTransport(Request $request)
    {
        $view = '';

        if ($request->bus) {
            try {
                $bus = Bus::with('route', 'transporter', 'pass', 'reservationTransports')->find($request->bus);
            } catch (ModelNotFoundException $e) {
                return $e->getMessage();
            }

            $view = view('transport::partials.busLine', ['bus' => $bus, 'id' => $request->id]);
            $view = $view->render();
        }

        echo $view;
    }
}
