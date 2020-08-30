<?php

namespace App\Http\Controllers;

use App\Http\Notification\Facade\Toastr;
use App\PassesSeller;
use App\Channel;
use App\Reservation;
use App\ViewReservation;
use Globobalear\Customers\Models\Customer;
use Globobalear\Resellers\Models\Reseller;
use Globobalear\Products\Models\TicketType;
use Globobalear\Products\Models\SeatType;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Globobalear\Payments\Models\PaymentMethod;
use Globobalear\Products\Models\Pass;
use Globobalear\Transport\Models\Bus;
use Globobalear\Menus\Models\Menu;
use Yajra\Datatables\Datatables;
use App\Role;
use App\Permission;
use App\RolePermission;
use Auth;
use DB;

class BookingListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect('/');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Auth::user()->role->role_permission('book-home-list')){
        
        $pass = Pass::find($id);
        if($pass){
        $chanells = Channel::with(['viewreservation' => function($query) use ($pass) {
            $query->whereHas('reservationPacks', function ($query) use ($pass) {
                $query->where('pass_id', $pass->id);   
            });
            $query->orwhere('pass_id', $pass->id)
            ->whereNull('canceled_date')
            ->where('finished',1);
        }])->get();

        $viewReservation = ViewReservation::query();
        $viewReservation->whereHas('reservationPacks', function ($query) use ($pass) {
            $query->where('pass_id', $pass->id);   
        });
        $viewReservation->orwhere('pass_id', $pass->id);
        $viewReservation->whereNull('canceled_date');
        $viewReservation->where('finished',1);
        $viewReservation->get();
        
        
        $totalADU = ViewReservation::getTotalADUSum($pass->id);
        $totalCHD = ViewReservation::getTotalCHDSum($pass->id);
        $totalINF = ViewReservation::getTotalINFSum($pass->id);
        $totalTOT = ViewReservation::getTotalTOTSum($pass->id);
    }
        /*$total = ViewReservation::select(DB::raw('SUM(ADU)AS ADU, SUM(CHD)AS CHD, SUM(INF)AS INF, SUM(TOT)AS TOT'))
        ->where('pass_id',$pass->id)->whereNull('canceled_date')->first();*/
        //dd($totalADU,$totalCHD,$totalINF,$totalTOT);
        //dd($viewReservation->get());
        //dd($viewReservation->first()->promocodes);
      
       return view('booking.index', compact('totalADU','totalCHD','totalINF','totalTOT','chanells','pass','viewReservation'));

       }else{
           abort(403);
       }

       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect('/');
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
        return redirect('/');
    }
}
