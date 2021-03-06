
@if(isset($reservation->reservationPacks) && !empty($reservation->reservationPacks || $reservation->ReservationPacksPirates) && !empty($reservation->ReservationPacksPirates))
@if ($reservation->reservationPacks->count() || $reservation->ReservationPacksPirates->count())
        <div class="panel panel-default">
        <div class="panel-heading">
        <h4><i class="fa fa-ticket"></i> {{$reservation->pack->title}}</h4>

         </div>


        <div class="panel-body">


        <table class="table table-hover table-responsive text-center">
            <thead class="thead-default">
            <tr>
                <td><b>TicketType</b></td>
                <td><b>Date Start</b></td>
                <td><b>Date End</b></td>

            </tr>
           </thead>
            <tbody>
             <tr>
                        @if ($reservation->reservationPacks->count())
                        <td>{{$reservation->reservationPacks->first()->TitleTicketType}}</td>
                        @else
                        <td>{{$reservation->ReservationPacksPirates->first()->TitleTicketType}}</td>
                        @endif
                        <td>{{$reservation->pack->date_start}}</td>
                        <td> {{$reservation->pack->date_end}}</td>
                         </tbody>

        </table>

         <table class="table table-hover table-responsive">
            <thead class="thead-default">
            <tr>
                <td>Products</td>
                <td>SeatType</td>
                <td>Pass</td>
                <td>Quantity</td>
                <td>Price</td>

            </tr>
           </thead>
            <tbody>
            @if ($reservation->reservationPacks->count())
            @foreach($reservation->reservationPacks as $item)
                    <tr>
                        <td>{{ $item->TitleProduct}}</td>
                        <td> {{$item->TitleSeaType }}</td>
                        <td>{{$item->DatePass->format('d-m-Y H:s') }}</td>
                          <td>{{$item->quantity }} </td>
                        <td>{{$item->unit_price }} € </td>
                    </tr>
            @endforeach
            @endif

            @if ($reservation->ReservationPacksPirates->count())
            @foreach($reservation->ReservationPacksPirates as $item)
                    <tr>
                        <td>{{ $item->TitleShow}}</td>
                        <td> {{$item->TitleSeaType }}</td>
                        <td>{{$item->DatePass->format('d-m-Y H:s') }}</td>
                        <td>{{$item->quantity }} </td>
                        <td>{{$item->unit_price }} € </td>
                    </tr>
            @endforeach
            @endif

            @if ($reservation->reservationPacksWristband)
                    <tr>
                        <td>{{ $reservation->reservationPacksWristband->titlewristband}}</td>
                        <td> - </td>
                        <td> - </td>
                        <td>{{$reservation->reservationPacksWristband->quantity }} </td>
                        <td>{{$reservation->reservationPacksWristband->unit_price }} € </td>
                    </tr>
             @endif
          
            </tbody>
            <tfoot>
            <tr>
                <th colspan="3"></th>
                <th>Total:</th>
                <th><b>{{ $reservation->getTicketPackPriceAttribute() }}</b>€</th>
            </tr>
            </tfoot>
        </table>

               

    
    <div class="panel panel-default">
    <div class="panel-heading">
    <h4> <i class="fa fa-eur"></i>
                {{ trans('common.final') }}   {{ trans('common.price') }}</h4>
        </div>
    <div class="panel-body">

    <table class="table table-hover table-responsive">
        
        <tbody>
        
             
            @if($reservation->booking_fee > 0)
                <tr>
                    <td><b><span class="text-danger"><i class="fa fa-money" aria-hidden="true"></i> Booking Fee: </span></b></td>
                    <td><b><span class="text-danger">{{ $reservation->booking_fee }} €</span></b></td>
                </tr>
            @endif

            @if($reservation->paypal > 0)
                <tr>
                    <td><b><span class="text-danger"><i class="fa fa-money" aria-hidden="true"></i> Paypal: </span></b></td>
                    <td><b><span class="text-danger">{{ $reservation->paypal }} %</span></b></td>
                </tr>
            @endif

            <tr>
                
                <th><span class="text-success"><i class="fa fa-money" aria-hidden="true"></i> Total:</span></th>
                <th><b><span class="text-success">{{ $reservation->getTotalPackPriceAttribute() }} </b>€</span></th>
            </tr>
           

            
        </tbody>
       
    </table>
    </div>
    </div>


        </div>
        </div>



@endif
@endif
