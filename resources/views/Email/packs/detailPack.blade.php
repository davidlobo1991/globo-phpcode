


@if ($reservation->reservationPacks->count() || $reservation->ReservationPacksPirates->count())

        <table width="900" border="0" cellspacing="0" cellpadding="0" style="color: #fff;font-size: 18px;">
        <tr>
        <td colspan="3" bgcolor="#CCCCCC"><h3>&emsp; {{ trans('common.pack') }} {{ trans('common.details') }} </h3></td>
        </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
                <td><b>TicketType</b></td>

            </tr>

            <tr>
            @if ($reservation->reservationPacks->count())
            <td>{{$reservation->reservationPacks->first()->TitleTicketType}}</td>
            @else
            <td>{{$reservation->ReservationPacksPirates->first()->TitleTicketType}}</td>
            @endif

        </table>

         <table width="900" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td colspan="6"><hr></td>
            </tr>
            <tr>
                <td><b>Products</b></td>
                <td><b>SeatType</b></td>
                <td><b>Pass</b></td>
                <td><b>Quantity</b></td>

                <td><b>Price</b></td>
            </tr>
            @if ($reservation->reservationPacks->count())
            @foreach($reservation->reservationPacks as $item)
                    <tr>
                        <td>{{ $item->TitleProduct or 'No found'}}</td>
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
                      <td>{{$item->TitleSeaType }}</td>
                      <td>{{$item->DatePass->format('d-m-Y H:s') }}</td>
                      <td>{{$item->quantity }} </td>

                      <td>{{$item->unit_price }} € </td>
                    </tr>
            @endforeach
            @endif

            {{-- @if ($reservation->reservationPacksWristband)
                    <tr>
                        <td>{{ $reservation->reservationPacksWristband->titlewristband}}</td>
                        <td> - </td>
                        <td> - </td>
                        <td>{{$reservation->reservationPacksWristband->quantity }} </td>
                          <td>{{$reservation->reservationPacksWristband->unit_price }} € </td>
                    </tr>
             @endif--}}

            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>

@endif


