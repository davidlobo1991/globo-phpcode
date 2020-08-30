@if(!$reservation->reservationTickets->isEmpty())
    <div class="panel panel-default">
        <div class="panel-heading">
        <h4><i class="fa fa-ticket"></i>
                    {{ trans('common.tickets') }}</h4>
         </div>
        <div class="panel-body">


        <table class="table table-hover table-responsive">
            <thead>
            <tr>
                <td>Seat Type</td>
                <td>Ticket Type</td>
                <td>Unit Price</td>
                <td>Quantity</td>
                <td>Price</td>
                <td>Final Price</td>
            </tr>
           </thead>
            <tbody>
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
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4"></th>
                <th>Total:</th>
                <th><b>{{ $reservation->getTicketPriceAttribute() }} </b>€</th>
            </tr>
            </tfoot>
        </table>

        </div>
    </div>
@endif

@if (!$reservation->reservationPacks->isEmpty())
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4><i class="fa fa-ticket"></i>{{ trans('common.tickets') }}</h4>
        </div>
        <div class="panel-body">
            <table class="table table-hover table-responsive">
                <thead>
                <tr>
                    <td>Pass</td>
                    <td>Seat Type</td>
                    <td>Ticket Type</td>
                    <td>Unit Price</td>
                    <td>Quantity</td>
                    <td>Price</td>
                    <td>Final Price</td>
                </tr>
               </thead>
                <tbody>
                @foreach($reservation->reservationPacks as $packline)
                    @if($packline->quantity > 0)
                        <tr>
                            <td>{{ $packline->passes->title }}</td>
                            <td>{{ $packline->seatTypes->title }}</td>
                            <td>{{ $packline->ticketType->title }}</td>
                            <td><b>{{ $packline->unit_price }}</b> €</td>
                            <td>{{ $packline->quantity }}</td>
                            <td ><b>{{ $packline->getPriceWODiscount() }} </b>€</td>
                            <td><b>{{ $packline->getTotalPriceAttribute() }} </b>€</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="4"></th>
                    <th>Total:</th>
                    <th><b>{{ $reservation->getTicketPackPriceAttribute() }} </b>€</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif
