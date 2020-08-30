<?php

namespace Globobalear\Promocodes\Controllers;

use App\Helpers\Helper;
use App\Http\Notification\Facade\Toastr;
use App\ReservationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Packs\Models\Pack;
use Globobalear\Payments\Traits\PromocodeSecure;
use Globobalear\Promocodes\Models\Promocode;
use Globobalear\Promocodes\Requests\PromocodeRequest;
use Globobalear\Promocodes\Traits\PromocodeAction;
use Globobalear\Products\Models\Pass;
use Globobalear\Products\Models\Product;
use Globobalear\Wristband\Models\Wristband;
use Yajra\Datatables\Datatables;
use Auth;

class PromocodesController extends Controller
{
    use PromocodeSecure;

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (!Auth::user()->role->role_permission('show_promocodes')) {
            abort(403, 'Unauthorized');
        }

        return view('promocodes::promocodes.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = Promocode::select('id', 'code', 'discount', 'canceled', 'single_use', 'valid_from', 'valid_to', 'for_from', 'for_to')
            ->get();

        return $datatables->collection($query)
            ->addColumn('action', 'promocodes::promocodes.actions')
            ->editColumn('discount', function ($q) {
                return $q->discount . '%';
            })
            ->editColumn('single_use', function ($q) {
                return $q->single_use ? 'Yes' : 'No';
            })
            ->editColumn('valid_from', function ($q) {
                return is_null($q->valid_from) ? '-' : $q->valid_from->format('Y/m/d');
            })
            ->editColumn('valid_to', function ($q) {
                return is_null($q->valid_to) ? '-' : $q->valid_to->format('Y/m/d');
            })
            ->editColumn('for_from', function ($q) {
                return is_null($q->for_from) ? '-' : $q->for_from->format('Y/m/d');
            })
            ->editColumn('for_to', function ($q) {
                return is_null($q->for_to) ? '-' : $q->for_to->format('Y/m/d');
            })
            ->setRowClass(function ($q) {
                return $q->canceled ? 'canceled' : '';
            })
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        if (!Auth::user()->role->role_permission('create_promocodes')) {
            abort(403, 'Unauthorized');
        }

        $products = Product::pluck('name', 'id');
        $packs = Pack::pluck('title', 'id');
        $wristbands = Wristband::pluck('title', 'id');

        return view('promocodes::promocodes.create', compact('products', 'packs', 'wristbands'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        if (!Auth::user()->role->role_permission('create_promocodes')) {
            abort(403, 'Unauthorized');
        }

        $this->validate($request, [
            'code' => 'required|unique:promocodes,code',
            'discount' => 'required',
        ]);

        $inputRequest = Helper::saveUncheckedCheckbox($request->all(), ['single_use', 'canceled']);
        $inputRequest = Helper::carbonParse($inputRequest, 'd/m/Y', ['valid_from', 'valid_to', 'for_from', 'for_to']);

        $promocode = Promocode::create($inputRequest);

        $promocode->products()->sync($request->products);
        $promocode->packs()->sync($request->packs);
        $promocode->wristbands()->sync($request->wristbands);

        Toastr::success('Promocode create successfully', 'Created!');

        return redirect()->route('promocodes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param Promocode $promocode
     * @return Redirect
     */
    public function show(Promocode $promocode)
    {
        if (!Auth::user()->role->role_permission('show_promocodes')) {
            abort(403, 'Unauthorized');
        }

        return redirect()->route('promocodes::promocodes.edit', $promocode->id);
    }

    /**
     * Promocode the form for editing the specified resource.
     *
     * @param  Promocode $promocode
     * @return View
     */
    public function edit(Promocode $promocode)
    {
        if (!Auth::user()->role->role_permission('edit_promocodes')) {
            abort(403, 'Unauthorized');
        }

        $products = Product::pluck('name', 'id');
        $packs = Pack::pluck('title', 'id');
        $wristbands = Wristband::pluck('title', 'id');

        $selectedProducts = $promocode->products->pluck('id');
        $selectedPacks = $promocode->packs->pluck('id');
        $selectedWristbands = $promocode->wristbands->pluck('id');

        return view('promocodes::promocodes.edit', compact(
                'products', 'selectedProducts', 'promocode', 'packs', 'wristbands', 'selectedPacks', 'selectedWristbands')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Promocode $promocode
     * @return Redirect
     */
    public function update(Request $request, Promocode $promocode)
    {
        if (!Auth::user()->role->role_permission('edit_promocodes')) {
            abort(403, 'Unauthorized');
        }

        $this->validate($request, [
            'code' => 'required|unique:promocodes,code,' . $promocode->id,
            'discount' => 'required',
        ]);

        $inputRequest = Helper::saveUncheckedCheckbox($request->all(), ['single_use', 'canceled']);
        $inputRequest = Helper::carbonParse($inputRequest, 'd/m/Y', ['valid_from', 'valid_to', 'for_from', 'for_to']);

        $promocode->update($inputRequest);

        $promocode->products()->sync($request->products);
        $promocode->packs()->sync($request->packs);
        $promocode->wristbands()->sync($request->wristbands);

        Toastr::success('Promocode updated successfully', 'Updated!');

        return redirect()->route('promocodes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Promocode $promocode
     * @return Redirect
     */
    public function destroy(Promocode $promocode)
    {
        if (!Auth::user()->role->role_permission('delete_promocodes')) {
            abort(403, 'Unauthorized');
        }

        $promocode->canceled = !$promocode->canceled;
        $promocode->save();

        if ($promocode->canceled) {
            Toastr::success('Promocode canceled successfully', 'Canceled!');
        } else {
            Toastr::success('Promocode activated successfully', 'Activated!');
        }

        return redirect()->route('promocodes.index');
    }


    /**
     * Return a parsed view with promocode validity confirmation
     *
     * @param AJAX Request $request
     */
    public function tryPromocode(PromocodeRequest $request)
    {
        $identifier = null;

        switch ($request->reservation_type_id) {
            case ReservationType::PRODUCTS:
                $identifier = $request->products[0]["pass_id"];
                break;
            case ReservationType::PACKS:
                $identifier = $request->products[0]["pack"];
                break;
        }

        $promocode = $this->getVerifiedPromocode($request->promocode, $request->reservation_type_id, $identifier);

        echo view('promocodes::partials.tryMessage', compact('promocode'))->render();
    }
}
