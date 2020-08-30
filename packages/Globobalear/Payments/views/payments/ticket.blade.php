 
@if(isset($reservation->reservationTickets) && !empty($reservation->reservationTickets))
@if(!$reservation->reservationTickets->isEmpty())  
<div class="panel panel-primary">
    <div class="panel-heading">
    <h4> <i class="fa fa-ticket"></i>
            {{ trans('common.ticket') }} {{ trans('common.details') }} </h4>
    </div>
    <div class="panel-body">
            <div class="overflow-scroll">
                    <table class="table table-hover table-responsive ">
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
                        <th><b>{{ $reservation->getTicketPriceAttribute() }}    </b>€</th>
                    
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
                                    <th><b><span class="text-success">{{ $reservation->getTotalPriceAttribute() }} </b>€</span></th>
                                </tr>
                            

                                
                            </tbody>
                        
                        </table>
                        </div>
                        </div>
            </div>
    </div>
</div>
@endif
@endif