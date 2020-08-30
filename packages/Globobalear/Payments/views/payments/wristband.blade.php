
@if(isset($reservation->reservationWristbandPasses) && !empty($reservation->reservationWristbandPasses))
    @if ($reservation->reservationWristbandPasses->count())
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>
                    <i class="fa fa-ticket"></i> {{ $reservation->reservationWristbandPasses[0]->title }}
                </h4>
            </div>
            <div class="panel-body">
                @foreach($reservation->reservationWristbandPasses as $item)
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-hover table-responsive text-center">
                                <thead class="thead-default">
                                <tr>
                                    <td><b>Date Start</b></td>
                                    <td><b>Date End</b></td>
                                    <td><b>Quantity</b></td>
                                    <td><b>Price</b></td>
                                </tr>
                                </thead>
                                <tbody>

                                <tr>
                                    <td>{{$item->date_start}}</td>
                                    <td> {{$item->date_end}}</td>
                                    <td> {{$item->pivot->quantity}}</td>
                                    <td> {{$item->price}}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td><b>Gross total</b></td>
                                    <td><b>{{ $reservation->totalPriceTicketWristbandPass }} €</b></td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-3">
                            <table class="table table-hover table-responsive text-center">
                                <thead class="thead-default">
                                <tr>
                                    <th>Products</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($item->products as $show)
                                    <tr>
                                        <td>{{ $products->name }}</td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>

                             
                        </div>
                    </div>

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
                                    <th><b><span class="text-success">{{ $reservation->getTotalWristbandsPriceAttribute() }} </b>€</span></th>
                                </tr>
                            

                                
                            </tbody>
                        
                        </table>
                        </div>
                        </div>
                @endforeach
            </div>
        </div>

    @endif
@endif
