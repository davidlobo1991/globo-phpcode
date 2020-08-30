

@if(isset($paid->reservation->reservationPacks) && !empty($paid->reservation->reservationPacks) || isset($paid->reservation->ReservationPacksPirates) && !empty($paid->reservation->ReservationPacksPirates))
@if ($paid->reservation->reservationPacks->count() || $paid->reservation->ReservationPacksPirates->count())

        <div class="panel panel-default">
        <div class="panel-heading">
        <h4><i class="fa fa-ticket"></i> {{$paid->reservation->pack->title}}</h4>
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
                         @if ($paid->reservation->reservationPacks->count())
                        <td>{{$paid->reservation->reservationPacks->first()->TitleTicketType}}</td>
                        @else
                        <td>{{$paid->reservation->ReservationPacksPirates->first()->TitleTicketType}}</td>
                        @endif
                      
                        <td>{{$paid->reservation->pack->date_start}}</td>
                        <td> {{$paid->reservation->pack->date_end}}</td>
                         </tbody>

        </table>

         <table class="table table-hover table-responsive">
            <thead class="thead-default">
            <tr>
                <td>Products</td>
                <td>SeatType</td>
                <td>Pass</td>
                <td>Quantity</td>
                <td>Commision</td>
                <td>Price</td>

            </tr>
           </thead>
            <tbody>
            @if($paid->reservation->reservationPacks)
            @foreach($paid->reservation->reservationPacks as $item)
                    <tr>
                        <td>{{ $item->TitleProduct or 'No found'}}</td>
                        <td> {{$item->TitleSeaType }}</td>
                        <td>{{$item->DatePass->format('d-m-Y H:s') }}</td>
                        <td>{{$item->quantity }} </td>
                        <td>{{$item->products->commission or 'No found'}} %</td>
                        <td>{{$item->unit_price }} € </td>
                    </tr>
            @endforeach
            @endif
            
            @if($paid->reservation->ReservationPacksPirates)
            @foreach($paid->reservation->ReservationPacksPirates as $item)
                    <tr>
                        <td>{{ $item->TitleShow}}</td>
                        <td> {{$item->TitleSeaType }}</td>
                        <td>{{$item->DatePass->format('d-m-Y H:s') }}</td>
                        <td>{{$item->quantity }} </td>
                        <td>- </td>
                        <td>{{$item->unit_price }} € </td>
                    </tr>
            @endforeach
            @endif

             @if ($paid->reservation->reservationPacksWristband)
                    <tr>
                        <td>{{ $paid->reservation->reservationPacksWristband->titlewristband}}</td>
                        <td> - </td>
                        <td> - </td>
                        <td>{{$paid->reservation->reservationPacksWristband->quantity }} </td>
                        <td> - </td>
                        <td>{{$paid->reservation->reservationPacksWristband->unit_price }} € </td>
                    </tr>
             @endif
            </tbody>

        </table>

        </div>
        </div>



@endif
@endif
