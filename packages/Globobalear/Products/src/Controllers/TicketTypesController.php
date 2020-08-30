<?php

namespace Globobalear\Products\Controllers;


use App\Helpers\Helper;
use App\Http\Notification\Facade\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Globobalear\Products\Models\TicketType;
use Globobalear\Products\Models\TicketTypePirates;
use Yajra\Datatables\Datatables;
use Auth;

class TicketTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        if (!Auth::user()->role->role_permission('show_tickettype')) {
            abort(403, 'Unauthorized');
        }

        return view('products::ticketTypes.index');
    }

    /**
     * Get data info to display
     *
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $query = TicketType::select('id', 'title', 'take_place', 'acronym', 'pirates_ticket_type_id', 'sort')
            ->orderBy('sort', 'asc')
            ->get()
            ->map(function ($item) {
                $item->pirates_ticket_type_title = $item->piratesTicketType ? $item->piratesTicketType->title : '';
                return $item;
            });

        return $datatables->collection($query)
            ->addColumn('action', 'products::ticketTypes.actions')
            ->orderColumn('sort', 'name $1')
            ->make(true);
    }

    /**
     * Create a new resource in storage.
     *
     * @return View
     */
    public function create()
    {
        if (!Auth::user()->role->role_permission('create_tickettype')) {
            abort(403, 'Unauthorized');
        }

        $piratesTicketTypes = ['' => 'No related'] + TicketTypePirates::all()->pluck('title', 'id')->toArray();

        return view('products::ticketTypes.create')
            ->with('piratesTicketTypes', $piratesTicketTypes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Redirect
     */
    public function store(Request $request)
    {
        if (!Auth::user()->role->role_permission('create_tickettype')) {
            abort(403, 'Unauthorized');
        }

        $this->validate($request, [
            'title' => 'required|unique:seat_types,title',
            'acronym' => 'required|unique:seat_types,acronym',
        ]);

        $inputRequest = Helper::saveUncheckedCheckbox($request->all(), ['take_place']);

        TicketType::create($inputRequest);

        Toastr::success('Ticket Type create successfully', 'Created!');

        return redirect()->route('ticketTypes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param TicketType $ticketType
     * @return Redirect
     */
    public function show(TicketType $ticketType)
    {
        if (!Auth::user()->role->role_permission('show_tickettype')) {
            abort(403, 'Unauthorized');
        }

        return redirect()->route('products::ticketTypes.edit', $ticketType->id);
    }

    /**
     * TicketType the form for editing the specified resource.
     *
     * @param  TicketType $ticketType
     * @return View
     */
    public function edit(TicketType $ticketType)
    {
        if (!Auth::user()->role->role_permission('edit_tickettype'))
            abort(403, 'Unauthorized');

        $piratesTicketTypes = ['' => 'No related'] + TicketTypePirates::all()->pluck('title', 'id')->toArray();

        return view('products::ticketTypes.edit')
            ->with('ticketType', $ticketType)
            ->with('piratesTicketTypes', $piratesTicketTypes);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  TicketType $ticketType
     * @return Redirect
     */
    public function update(Request $request, TicketType $ticketType)
    {
        if (!Auth::user()->role->role_permission('edit_tickettype')) {
            abort(403, 'Unauthorized');
        }

        $this->validate($request, [
            'title' => 'required|unique:seat_types,title,' . $ticketType->id,
            'acronym' => 'required|unique:seat_types,acronym,' . $ticketType->id,
        ]);

        $inputRequest = Helper::saveUncheckedCheckbox($request->all(), ['take_place']);

        $ticketType->update($inputRequest);

        Toastr::success('Ticket Type updated successfully', 'Updated!');

        return redirect()->route('ticketTypes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TicketType $ticketType
     * @return Redirect
     */
    public function destroy(TicketType $ticketType)
    {
        if (!Auth::user()->role->role_permission('delete_tickettype')) {
            abort(403, 'Unauthorized');
        }



        try {
            $ticketType->forceDelete();
            Toastr::success(trans('menu.ticketTypes') . trans('flash.deleted') . trans('flash.successfully'), 'success');
            return redirect()->route('ticketTypes.index');
            } catch (\Exception $e) {
            Toastr::error('You can not delete the Ticket Type because it has associated pass');
            return redirect()->route('ticketTypes.index');
            }
    }
}
