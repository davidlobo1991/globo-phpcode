@if(!$reservation->ReservationTransport->isEmpty())
    <div class="panel panel-info">
    <div class="panel-heading">
    <h4> <i class="fa fa-bus"></i>
                {{ trans('common.transporter') }} {{ trans('common.details') }} </h4>
    </div>
    <div class="panel-body">
            <table class="table table-hover table-responsive">
                <thead>
                <tr>
                    <td>Route</td>
                    <td>Pickup Point</td>
                    <td>Hour</td>
                    <td>Unit Price</td>
                    <td>Quantity</td>
                    <td>Final Price</td>
                </tr>
                </thead>
                <tbody>
                @foreach($reservation->ReservationTransport as $bus)
                    <tr>
                        <td>{{ $bus->RouteTitle }}</td>
                        <td>{{ $bus->pickup_point }}</td>
                        <td>{{ $bus->hour }}</td>
                        <td ><b>{{ $bus->price }} </b>€</td>
                        <td>{{ $bus->quantity }}</td>
                        <td><b>{{ $bus->price * $bus->quantity }}</b> €</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4"></th>
                        <th>Total:</th>
                        <th>{{ $reservation->getTransportPriceAttribute() }} €</th>
                    </tr>
                </tfoot>
            </table>
    
    </div>
    </div>
@endif