<?php

namespace App\Http\Controllers;

use App\Http\Notification\Facade\Toastr;
use App\PassesSeller;
use App\Reservation;
use App\ViewReservation;
use App\GlobalConf;
use App\Channel;
use App\ReservationType;
use Globobalear\Customers\Models\Customer;
use Globobalear\Resellers\Models\Reseller;
use Globobalear\Packs\Models\Pack;
use Globobalear\Packs\Models\PackProduct;
use Globobalear\Packs\Models\PackShowPirates;
use Globobalear\Promocodes\Models\Promocode;
use Carbon\Carbon;
use App\Http\Requests\ReservationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Traits\HasEmail;
use Globobalear\Payments\Models\PaymentMethod;
use Globobalear\Products\Models\Product;
use Globobalear\Products\Models\Provider;
use Globobalear\Products\Models\Pass;
use Globobalear\Transport\Models\Bus;
use Globobalear\Menus\Models\Menu;
use Yajra\Datatables\Datatables;
use App\Role;
use App\Permission;
use App\RolePermission;
use Barryvdh\DomPDF\Facade as PDF;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Log;

class ReservationsController extends Controller
{
    use HasEmail;

    /**
     * Display a listing of the resource.
     * @return View
     */
    public function index() : View
    {
        if (Auth::user()->role->role_permission('view_reservations')) {

            $passesSellers = PassesSeller::get();
            $reservationstypes = ReservationType::get();

            $products = Product::get();
            $passes = Pass::get();
            $providers = Provider::get();
            $channels = Channel::get();

            return view('reservations.index')
                ->with('passesSellers', $passesSellers)
                ->with('reservationstypes', $reservationstypes)
                ->with('products', $products)
                ->with('passes', $passes)
                ->with('providers', $providers)
                ->with('channels', $channels);
        } else {
            abort(403);
        }
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return JsonResponse
     * @throws \Exception
     */
    public function data(Datatables $datatables) : JsonResponse
    {
        /*
         * Los datos del booking search son enviados por los headers de la request i
         * NO PUEDEN SER DEFINIDOS CON BARRAS BAJAS
         */

        $query = ViewReservation::orderBy('created_at', 'desc')->where('finished', 1)->whereNull('canceled_date');

        $data = $this->getRequestAndHeaderData(request());

        $this->applyBookingSearchFilters($query, $data);

        return $datatables->eloquent($query)
            ->addColumn('action', 'reservations.actions')
            ->editColumn(
                'datetime', function (ViewReservation $query) {
                    return Carbon::parse($query->datetime)->format('d/m/Y H:i');
                }
            )
            ->editColumn(
                'created_at', function (ViewReservation $query) {
                    return Carbon::parse($query->created_at)->format('d/m/Y H:i:s');
                }
            )
            ->editColumn(
                'email', function (ViewReservation $query) {
                    return '<a href="'. route("customers.edit", ["id" => $query->customer_id]) .'">' . $query->email .'</a>';
                }
            )
            ->orderColumn('created_at', 'name $1')
            ->orderColumn('products.name', 'name $1')
            ->orderColumn('passes.datetime', 'name $1')
            ->rawColumns([0])
            ->make(true);
    }

    public function getRequestAndHeaderData(Request $request)
    {
        return [
            'reservationNumber' => request()->header('reservationNumber') ?? $request->reservationNumber,
            'email' => request()->header('email') ?? $request->email,
            'promocode' => request()->header('promocode') ?? $request->promocode,
            'createdAtFrom' => request()->header('createdAtFrom') ?? $request->createdAtFrom,
            'createdAtTo' => request()->header('createdAtTo') ?? $request->createdAtTo,
            'passFrom' => request()->header('passFrom') ?? $request->passFrom,
            'passTo' => request()->header('passTo') ?? $request->passTo,
            'productId' => request()->header('productId') ?? $request->productId,
            'passId' => request()->header('passId') ?? $request->passId,
            'providerId' => request()->header('providerId') ?? $request->providerId,
            'channelId' => request()->header('channelId') ?? $request->channelId,
        ];
    }

    public function applyBookingSearchFilters($query, $data)
    {
        /* Reservation Number */
        if (!empty($data['reservationNumber'])) {
            $reservationNumber = $data['reservationNumber'];

            $query->where(function ($query) use ($reservationNumber) {
                $query->where('reservation_number', 'LIKE', '%'.$reservationNumber.'%')
                    ->orWhere('reservation_number', 'LIKE', '%'.strtolower($reservationNumber).'%')
                    ->orWhere('reservation_number', 'LIKE', '%'.strtoupper($reservationNumber).'%');
            });
        }

        /* Email */
        if (!empty($data['email'])) {
            $email = $data['email'];

            $query->where(function ($query) use ($email) {
                $query->where('email', 'LIKE', '%'.$email.'%')
                    ->orWhere('email', 'LIKE', '%'.strtolower($email).'%')
                    ->orWhere('email', 'LIKE', '%'.strtoupper($email).'%');
            });
        }

        /* Reservation Number */
        if (!empty($data['promocode'])) {
            $promocodeCode = $data['promocode'];

            $promocodes = Promocode::where('code', 'LIKE', '%'.$promocodeCode.'%')
                ->orWhere('code', 'LIKE', '%'.strtolower($promocodeCode).'%')
                ->orWhere('code', 'LIKE', '%'.strtoupper($promocodeCode).'%')
                ->get();

            $query->whereIn('promocode_id', $promocodes->pluck('id'));
        }

        /* Created at */
        if (!empty($data['createdAtFrom'])) {
            $createdAtFrom = Carbon::createFromFormat('d-m-Y', $data['createdAtFrom'])->startOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '>=', $createdAtFrom);
        }
        if (!empty($data['createdAtTo'])) {
            $createdAtTo = Carbon::createFromFormat('d-m-Y', $data['createdAtTo'])->endOfDay()->format('Y-m-d H:i:s');
            $query->where('created_at', '<=', $createdAtTo);
        }

        /* Pass date */
        if (!empty($data['passFrom'])) {
            $passFrom = Carbon::createFromFormat('d-m-Y', $data['passFrom'])->startOfDay()->format('Y-m-d H:i:s');
            $query->where(function ($query) use ($passFrom) {
                $query->whereHas('pass', function ($query) use ($passFrom) {
                    $query->where('datetime', '>=', $passFrom);
                })
                    ->orWhereHas('reservationPacks.passes', function ($query) use ($passFrom) {
                        $query->where('datetime', '>=', $passFrom);
                    })
                    ->orWhereHas('reservationPacksPirates.passes', function ($query) use ($passFrom) {
                        $query->where('datetime', '>=', $passFrom);
                    });
            });
        }
        if (!empty($data['passTo'])) {
            $passTo = Carbon::createFromFormat('d-m-Y', $data['passTo'])->endOfDay()->format('Y-m-d H:i:s');
            $query->where(function ($query) use ($passTo) {
                $query->whereHas('pass', function ($query) use ($passTo) {
                    $query->where('datetime', '<=', $passTo);
                })
                    ->orWhereHas('reservationPacks.passes', function ($query) use ($passTo) {
                        $query->where('datetime', '<=', $passTo);
                    })
                    ->orWhereHas('reservationPacksPirates.passes', function ($query) use ($passTo) {
                        $query->where('datetime', '<=', $passTo);
                    });
            });
        }

        /* Product ID */
        if (!empty($data['productId'])) {
            $productId = (int) $data['productId'];
            $query->where(function ($query) use ($productId) {
                $query->whereHas('reservation.pass', function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                    ->orWhereHas('reservationPacks', function ($query) use ($productId) {
                        $query->where('product_id', $productId);
                    })
                    ->orWhereHas('reservationPacksPirates', function ($query) use ($productId) {
                        $query->where('product_id', $productId);
                    });
            });
        }

        /* Pass ID */
        if (!empty($data['passId'])) {
            $passId = (int) $data['passId'];
            $query->where(function ($query) use ($passId) {
                $query->where('pass_id', $passId)
                    ->orWhereHas('reservationPacks', function ($query) use ($passId) {
                        $query->where('pass_id', $passId);
                    })
                    ->orWhereHas('reservationPacksPirates', function ($query) use ($passId) {
                        $query->where('pass_id', $passId);
                    });
            });
        }

        /* Provider ID */
        if (!empty($data['providerId'])) {
            $providerId = (int) $data['providerId'];

            $query->where(function ($query) use ($providerId) {
                $query->whereHas('reservation.pass.product', function ($query) use ($providerId) {
                    $query->where('provider_id', $providerId);
                })
                    ->orWhereHas('reservationPacks.products', function ($query) use ($providerId) {
                        $query->where('provider_id', $providerId);
                    })
                    ->orWhereHas('reservationPacksPirates.shows', function ($query) use ($providerId) {
                        $query->where('provider_id', $providerId);
                    });
            });
        }

        /* Channel ID */
        if (!empty($data['channelId'])) {
            $channelId = (int) $data['channelId'];
            $query->where('channel_id', $channelId);
        }
    }

    public function excel(Request $request)
    {
        $query = ViewReservation::orderBy('created_at', 'desc')->where('finished', 1)->whereNull('canceled_date');

        $data = $this->getRequestAndHeaderData($request);

        $this->applyBookingSearchFilters($query, $data);

        $reservations = $query->get();

        $currentDate = Carbon::now()->format('_d_m_Y_H:i:s');
        $title = 'Reservation-' . $currentDate;

        //return view('reservations.excel', ['reservations' => $reservations]);

        Excel::create($title, function ($excel) use ($reservations) {
            $excel->sheet('Reservation', function ($sheet) use ($reservations) {
                $sheet->setColumnFormat(array(
                    'A' => '@',
                    'E' => '0',
                    'F' => '0',
                    'G' => '0',
                    'H' => '0',
                    'I' => '0',
                    'J' => '0',
                ));

                //dump($reservations[0]->reservation_table[0]->id,$reservations );
                $sheet->loadView('reservations.excel')
                    ->with("reservations", $reservations);
            });
        })->download('xls');
    }

    /**
     * Resevations Canceled the form for creating a new resource.
     * @return View
     */
    public function canceled()
    {
        if(Auth::user()->role->role_permission('view_reservations')){

            return view('reservations.canceled');

        }else{
            abort(403);
        }
    }

    public function datacanceled(Datatables $datatables): JsonResponse
    {
        $query = ViewReservation::orderBy('created_at','desc')->whereNotNull('canceled_date');

        return $datatables->eloquent($query)
            ->addColumn('action', 'reservations.actionsCanceled')
            ->editColumn('datetime', function ($q) {
                return Carbon::parse($q->datetime)->format('d/m/Y H:i');
            })
            ->editColumn('created_at', function ($q) {
                return Carbon::parse($q->created_at)->format('d/m/Y H:i:s');
            })

            ->editColumn('email', function ($q) {
                return '<a href="'. route("customers.edit",["id"=>$q->customer_id]) .'">' . $q->email .'</a>';
            })

            ->orderColumn('created_at', 'name $1')
            ->orderColumn('products.name', 'name $1')
            ->orderColumn('passes.datetime', 'name $1')
            ->rawColumns([0])
            ->make(true);
    }

    /**
     * Resevations Canceled the form for creating a new resource.
     * @return View
     */
    public function unfinished()
    {
        if(Auth::user()->role->role_permission('view_reservations')){
            return view('reservations.unfinished');
        }else{
            abort(403);
        }
    }

    public function dataunfinished(Datatables $datatables): JsonResponse
    {
        $query = ViewReservation::orderBy('created_at','desc')->where('finished',0)->whereNull('canceled_date');

        return $datatables->eloquent($query)
            ->addColumn('action', 'reservations.actionsUnfinished')
            ->editColumn('datetime', function ($q) {
                return Carbon::parse($q->datetime)->format('d/m/Y H:i');
            })
            ->editColumn('created_at', function ($q) {
                return Carbon::parse($q->created_at)->format('d/m/Y H:i:s');
            })

            ->editColumn('email', function ($q) {
                return '<a href="'. route("customers.edit",["id"=>$q->customer_id]) .'">' . $q->email .'</a>';
            })

            ->orderColumn('created_at', 'name $1')
            ->orderColumn('products.name', 'name $1')
            ->orderColumn('passes.datetime', 'name $1')
            ->rawColumns([0])
            ->make(true);
    }


    /**
     * Resevations Canceled the form for creating a new resource.
     * @return View
     */
    public function deleted()
    {
        if(Auth::user()->role->role_permission('view_reservations')){
            return view('reservations.deleted');
        }else{
            abort(403);
        }
    }

    public function datadeleted(Datatables $datatables): JsonResponse
    {
        $query = ViewReservation::onlyTrashed()->orderBy('created_at','desc');

        return $datatables->eloquent($query)
            ->addColumn('action', 'reservations.actionsDeleted')
            ->editColumn('datetime', function ($q) {
                return Carbon::parse($q->datetime)->format('d/m/Y H:i');
            })
            ->editColumn('created_at', function ($q) {
                return Carbon::parse($q->created_at)->format('d/m/Y H:i:s');
            })

            ->editColumn('email', function ($q) {
                return '<a href="'. route("customers.edit",["id"=>$q->customer_id]) .'">' . $q->email .'</a>';
            })

            ->orderColumn('created_at', 'name $1')
            ->orderColumn('products.name', 'name $1')
            ->orderColumn('passes.datetime', 'name $1')
            ->rawColumns([0])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @param $passSellerId
     * @return View
     */
    public function create(Request $request): View
    {

        if (Auth::user()->role->role_permission('create_reservations')) {
            $passSellerId = $request->id;
            $passId = isset($request->pass) ? $request->pass : null;

            $passSeller = PassesSeller::with('channels')->find($passSellerId);
            $resellers = Reseller::where('passes_seller_id', $passSellerId)->orderBy('company')->pluck('company', 'id');
            $channels = Channel::where('passes_seller_id',1)->where('is_enable',1)->pluck('name','id');
            $global = GlobalConf::first();
            $product = [];
            if (! empty($request->product)) {
                $product = Product::find($request->product);
                if (! empty($product)) {
                    $product = ['id' => $product->id, 'title' => $product->title];
                }
            }
            $pass = [];
            if (! empty($request->pass)) {
                $pass = Product::find($request->pass);
                if (! empty($pass)) {
                    $pass = [$pass->id, $pass->title];
                }
            }


            return view('reservations.create')
                ->with('global', $global)
                ->with('channels', $channels)
                ->with('passSeller', $passSeller)
                ->with('resellers', $resellers)
                ->with('product', $product)
                ->with('pass', $pass);

        } else {
            abort(403);
        }
    }

    /**
     * @param ReservationRequest $request
     * @return RedirectResponse
     *
     */
    public function store(ReservationRequest $request): RedirectResponse
    {
        if(! Auth::user()->role->role_permission('create_reservations')){
            return abort(403);
        }
        $inputData = $request->all();

        //Verifica si el customer existe
        if (empty($inputData['customer_id'])) {
            $customer = $this->storeCustomer($inputData);
            $inputData['customer_id'] = $customer;
        }

        if (isset($inputData['passes'])) {
            $request->merge(['pass_id' => $inputData['passes']]);
        }

        $request->merge(['product_id' => $inputData['products']]);
        $request->merge(['created_by' => \Auth::user()->id]);
        $request->merge(['reservation_number' => strtoupper(uniqid(config('crs.reservation-number-prfix')))]);

        $reservation = Reservation::create($request->all());
        $reservation->reservation_type_id = ReservationType::PRODUCTS;

        /** TODO: Start Reservations Tickets */
        if (isset($request->data)) {
            Pass::generateReservationTickets($request->data, $reservation);
            $reservation->save();
            if ($reservation->pendingToPay) {
                $reservation->finished = 0;
                return redirect()->route('payments.getPayments', [$reservation->id]);
            }

        }

        $reservation->save();

        Log::info('Create reservations. #' . trans('common.reservation_number') .':'. $reservation->reservation_number .' # User By: ' .\Auth::user()->name);

        Toastr::success('Reservation created successfully', 'Created!');

        return redirect()->route('reservations.index');
    }

    public function storeCustomer($inputData)
    {
        $inputData = [
            'name' => $inputData['name'],
            'phone' => $inputData['phone'],
            'email' => $inputData['email'],
            'identification_number' => $inputData['identification_number']
        ];

        $customer = Customer::where('email',$inputData['email'])->first();
        if (!isset($customer)) {
            $customer = Customer::create($inputData);
        }

        return $customer->id;
    }

    /**
     * @param  Request $request
     * @return Response
     */
    public function cancel(Request $request)
    {
        try {
            $reservation = Reservation::findOrFail($request->id);
            $reservation->canceled_by = \Auth::user()->id;
            $reservation->canceled_date = Carbon::now()->toDateTimeString();
            $reservation->canceled_reason = $request->reason;
            $reservation->save();
        } catch (\Exception $e) {
            Toastr::error('Reservation Canceled ', 'Error Canceled!');
            return redirect(route('reservations.index'));
        }
        Toastr::success('Reservation Canceled', 'Canceled successfully!');

        return redirect(route('reservations.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reservation $reservation
     * @return RedirectResponse
     */
    public function show(Reservation $reservation)
    {

        if (Auth::user()->role->role_permission('show_reservations')) {

            return view('reservations.show')
                ->with('reservation', $reservation);
        } else {
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reservation $reservation
     * @return \Illuminate\Http\Response
     */
    public function edit(Reservation $reservation)
    {
        if (Auth::user()->role->role_permission('edit_reservations')) {
            $reservation->load('channel.passesSeller', 'pass.product', 'product', 'pass');

            $passSeller = $reservation->channel->passesSeller;

            $channels = Channel::where('passes_seller_id', 1)->where('is_enable', 1)->pluck('name', 'id');
            $resellers = Reseller::where('passes_seller_id', $passSeller->id)->pluck('company', 'id');
            $global = GlobalConf::first();

            return view('reservations.edit')
                ->with('global', $global)
                ->with('channels', $channels)
                ->with('passSeller', $passSeller)
                ->with('reservation', $reservation)
                ->with('resellers', $resellers);
        } else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Reservation $reservation
     * @return \Illuminate\Http\Response
     */
    public function update(ReservationRequest $request, Reservation $reservation)
    {
        $inputData = $request->all();

        if (!isset($inputData['booking_fee'])) {
            $inputData['booking_fee'] = 0;
        }

        $reservation->update($inputData);

        $reservation->save();

        Pass::generateReservationTickets($inputData['data'], $reservation);

        Bus::generateReservationTransports($inputData, $reservation);

        Menu::generateReservationMenu($inputData, $reservation);

        if ($reservation->pendingToPay) {
            $reservation->finished = 0;

            return redirect()->route('payments.getPayments', [$reservation->id]);
        }

        $reservation->finished = 1;
        $reservation->save();

        Toastr::success('Reservation updated successfully', 'Updated!');

        return redirect()->route('reservations.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reservation $reservation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reservation $reservation)
    {
        if (Auth::user()->role->role_permission('delete_reservations')) {
            $reservation->delete();
            Toastr::success('Reservation ' . trans('flash.deleted') . trans('flash.successfully'), 'success');
            return redirect()->route('reservations.index');
        } else {
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage. Unfinished Reservations.
     *
     * @param  \App\Reservation $reservation
     * @return \Illuminate\Http\Response
     */
    public function destroyUnfinished(Reservation $reservation)
    {
        if (Auth::user()->role->role_permission('delete_reservations')) {
            $reservation->delete();
            Toastr::success('Reservation ' . trans('flash.deleted') . trans('flash.successfully'), 'success');
            return redirect()->route('reservations.unfinished');
        } else {
            abort(403);
        }
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  \App\Reservation $reservation
     * @return \Illuminate\Http\Response
     */
    public function restore(Reservation $reservation,$id)
    {
        if (Auth::user()->role->role_permission('restore_reservations')) {

            $reservation = $reservation::onlyTrashed()->find($id);
            $reservation->deleted_at = null;
            $reservation->save();
            Toastr::success('Reservation ' . trans('flash.restored') . trans('flash.successfully'), 'success');
            return redirect()->route('reservations.index');
        } else {
            abort(403);
        }
    }

    /**
     * Show PDF.
     *
     * @param  \App\Reservation $reservation
     * @return \Illuminate\Http\Response
     */
    public function pdf(Reservation $reservation,$id)
    {
        if (Auth::user()->role->role_permission('show_reservations')) {
            $reservation = Reservation::find($id);

            $pdf = PDF::loadView(
                'reservations.pdf', [
                    'reservation' => $reservation,
                ]
            );

            return $pdf->stream('reservation.pdf');
        } else {
            abort(403);
        }
    }

    public function resendEmail(Request $request)
    {
        $id = $request->id_reservation;
        $reservation = ViewReservation::find($id);
        $resultado = $this->sendMailReservation($id);

        if ($resultado == true) {
            Toastr::success('Send Reservation '. $reservation->reservation_number .' '. trans('flash.email') . trans('flash.successfully'), 'success');
        } else {
            Toastr::success('Send Reservation '. $reservation->reservation_number .' '. trans('flash.email') . trans('flash.failure'), 'success');
        }

        return redirect()->route('reservations.index');
    }
}
