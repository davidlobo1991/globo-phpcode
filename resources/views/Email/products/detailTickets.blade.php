@if($reservation->count())
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
        <td><b>Seat Type</b></td>
        <td><b>Ticket Type</b></td>
        <td><b>Unit Price</b></td>
        <td><b>Quantity</b></td>
        <td><b>Price</b></td>
        <td><b>Final Price</b></td>
      </tr>
@foreach($reservation->reservationTickets as $ticket)
                @if($ticket->quantity > 0)
                <tr>
                  <td>{{ $ticket->SeatTypeTitle }}</td>
                  <td>{{ $ticket->TicketTypeTitle }}</td>
                  <td><b>{{ $ticket->unit_price }}</b> €</td>
                  <td>{{ $ticket->quantity }}</td>
                  <td ><b>{{ $ticket->getPriceWODiscount() }} </b>€</td>
                  <td><b>{{ $ticket->getTotalPriceAttribute() }} </b>€</td>
                </tr>
@endif
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
  <td bgcolor="#E2E2E2"><b>Total: {{ $reservation->getTicketPriceAttribute() }} </b>€</td>
</tr>
<tr>
  <td  colspan="6">&nbsp;</td>

</tr>
<tr></tr>
    </table>

@endif
