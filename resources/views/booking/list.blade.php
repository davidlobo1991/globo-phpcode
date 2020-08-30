 
 @if($pass)
 <div class="col-md-12" >
    <div class="box">
        <div class="panel-group">
            <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4> <i class="fa fa-book"></i> {{ $pass->title }}</h4>
                </div>
            </div>
            <div class="panel-body">
 @if($viewReservation)
    <div class="overflow-scroll">
    <table class="table table-hover table-responsive">
    @foreach($chanells as $chanell)
        <thead>
            <tr class="info">
                <td colspan="7">
                <h4><i class="fa fa-th" aria-hidden="true"></i> {{$chanell->name}}</h4>
                </td>
            </tr>
        </thead>
            <tbody>
                <thead class="thead-default">
                <tr>
                    <td>Res. Number</td>
                    <td>Name</td>
                    <td>ADU</td>
                    <td>CHD</td>
                    <td>INF</td>
                    <td>TOT</td>
                    
                </tr>
                </thead>
                    @foreach($chanell->viewreservation as $reservation)
                            <tr>
                                <td><a href="{{route($reservation->reservation_type_id.'.product',['id'=>$reservation->id])}}">{{ $reservation->reservation_number }}</a></td>
                                <td>{{ $reservation->name }}  </td>
                                 @if ($reservation->reservationPacks->count())
                                 @foreach($reservation->getAvailabilityPack($pass->id,$reservation->id) as $pack)
                                  <td>{{ $pack->ADU }}  </td> 
                                  <td>{{ $pack->CHD }}  </td>
                                  <td>{{ $pack->INF }}  </td>
                                  <td>{{ $pack->TOT }}  </td>
                                 @endforeach
                               
                                @else
                                <td>{{ $reservation->ADU }}  </td>
                                <td>{{ $reservation->CHD }}  </td>
                                <td>{{ $reservation->INF }}  </td>
                                <td>{{ $reservation->TOT }}  </td>
                                @endif
                            </tr>
                            <tr>
                                <td colspan="7"> 
                                <div class="box-header"> 
                                    <div class="pull-right">
                                            <button type="button" data-toggle="collapse" data-target="#tickets{{$reservation->id}}" aria-expanded="false" aria-controls="collapseExample">
                                            <i class="fa fa-chevron-down" aria-hidden="true"></i> {{ trans('common.details')}}
                                            </button>
                                    </div>
                                </div>
                                    <div class="collapse" id="tickets{{$reservation->id}}">
                                    @if ($reservation->reservationPacks->count())
                                        @include('booking.packs.detailPack')
                                        @include('booking.packs.detailTransport')
                                        @include('booking.packs.detailPromocode')
                                        @include('booking.packs.detailPrice')
                                    @endif
                                    @if(!$reservation->reservationTickets->isEmpty())
                                        @include('booking.products.detailTickets')
                                        @include('booking.products.detailTransport')
                                        @include('booking.products.detailPromocode')
                                        @include('booking.products.detailPrice')
                                    @endif
                                   
                                    @include('booking.comment')
                                        
                                    </div> 
                                </td>
                            </tr>
                        @endforeach
                </tbody>
            @endforeach
                    </table>
                    </div>
                        <div class="panel-group">
                        <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4> <i class="fa fa-book"></i> Total: {{ $pass->title }}</h4>
                            </div>
                        </div>
                        <div class="panel-body">
                       

                         <div class="col-md-12 text-center" >
                                <div class="col-md-3" ><h3><b><mark>ADU:</mark></b> <span class="bg-success">{{$totalADU}}</span></h3></div>
                                <div class="col-md-3" ><h3><b><mark>CHD:</mark></b> <span class="bg-success">{{$totalCHD}}</span></h3></div>
                                <div class="col-md-3" ><h3><b><mark>INF:</mark></b> <span class="bg-success">{{$totalINF}}</span></h3></div>
                                <div class="col-md-3" ><h3><b><mark>TOT:</mark></b> <span class="bg-success">{{$totalTOT}}</span></h3></div>
                        </div>
                        </div>
        @else

        <p>Sorry, but there are no reservations for the pass. </p>    
        @endif


        </div>
    </div>
</div>
 @endif