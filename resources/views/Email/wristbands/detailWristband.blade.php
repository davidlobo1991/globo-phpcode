@if(isset($reservation->reservationWristbandPasses) && !empty($reservation->reservationWristbandPasses))
@if ($reservation->reservationWristbandPasses->count())
    @foreach($reservation->reservationWristbandPasses as $item)
     <h4><i class="fa fa-ticket"></i> {{$item->title}}</h4>
    <table width="900" border="0" cellspacing="0" cellpadding="0">
      <tr align="center">
        <td colspan="6">&nbsp;</td>
      </tr>
      <tr align="center">
       
         <td colspan="6" bgcolor="#CCCCCC"><h3>&emsp; {{ trans('common.ticket') }} {{ trans('common.details') }} </h3></td>
      </tr>
      <tr>
        <td colspan="6">&nbsp;</td>
      </tr>
      <tr>
                <td><b>Date Start</b></td>
                <td><b>Date End</b></td>
                <td><b>Quantity</b></td>
                <td><b>Price</b></td>
      </tr>
                @foreach($reservation->reservationWristbandPasses as $item)
                
                <tr>
                <td>{{$item->date_start}}</td>
                <td> {{$item->date_end}}</td>
                <td> {{$item->quantity}}</td>
                <td> {{$item->price}}</td>
                </tr>
            @endforeach
          <tr>
            <td colspan="6"><hr></td>
          </tr>
          <tr>
            <td colspan="5"></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="5" bgcolor="#E2E2E2"></td>
            <td bgcolor="#E2E2E2"><b>Total: {{ $reservation->totalPriceTicketWristbandPass }}</b>€</td>
          </tr>
          <tr>
            <td  colspan="6">&nbsp;</td>
            
          </tr>
          <tr></tr>
              </table>
    @endforeach
@endif   
@endif