

@if($reservation)

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4> <i class="fa fa-eur"></i>
                {{ trans('common.final') }}   {{ trans('common.price') }}</h4>
        </div>
        <div class="panel-body">

            <table class="table table-hover table-responsive">

                <tbody>
                 <tfoot>
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
                <th><b><span class="text-success">{{ $reservation->getTotalWristbandsPriceAttribute() }} </b>€</span></th>
            </tr>
            </tfoot>

           
            </table>
        </div>
    </div>
@endif