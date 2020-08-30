 <div class="col-md-12">

    @foreach($paymentmethods as $method)
        <table class="table table-striped table-hover table-condensed table-responsive">
        <h4><i class="fa fa-money"></i> {{ $method->name }}</h4>
        <thead class="thead-default">
            <tr>
                <td class="col-lg-auto">Reservation </td>
                <td class="col-lg-auto">Phone </td>
                <td class="col-lg-auto">Type </td>
                <td class="col-lg-auto">{{ trans('common.show') }}</td>
                <td class="col-lg-auto">Paid </td>
                <td class="col-lg-auto">Date </td>
            </tr>
        </thead>
            <tbody>
            @if(!$payments->isEmpty())
            @foreach($payments->where('payment_method_id',$method->id) as $paid)

                    <tr>
                        <td><a href="{{ route($paid->reservation_type_id.'.show', $paid->reservation_id) }}">
                        {{ $paid->reservation_number }} - {{ $paid->customers_name }}
                        </a></td>
                        <td>{{ $paid->customers_phone }} </td>
                        <td>{{ $paid->type }} </td>
                        <td>{{ $paid->name_reservation }} </td>
                        <td>{{ $paid->total }} </td>
                        <td>{{ $paid->created_at->format('d-m-Y H:i') }} </td>
                    </tr>

                    <tr>
                    <td colspan="6">
                    <div class="box-header">
                        <div class="pull-right">
                                <button type="button" data-toggle="collapse" data-target="#reservation{{$paid->id}}" aria-expanded="false" aria-controls="collapseExample">
                                <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                </button>
                        </div>
                    </div>
                        <div class="collapse" id="reservation{{$paid->id}}">
                            @if ($paid->reservation->reservationPacks->count() || $paid->reservation->ReservationPacksPirates->count())
                                @include('payments::payments.partialsPayment.pack')
                            @endif
                            @if(!$paid->reservation->reservationTickets->isEmpty())
                                @include('payments::payments.partialsPayment.ticket')
                            @endif
                            @if ($paid->reservation->reservationWristbandPasses->count())
                                @include('payments::payments.partialsPayment.wristband')
                            @endif

                            @include('payments::payments.partialsPayment.promocode')
                        </div>
                    </td>
                </tr>

            @endforeach
            @endif
        </tbody>


                <tfoot>
                            <tr >
                        <th colspan="5"></th>

                        <th>Total: <span class="text-success">{{number_format($payments->where('payment_method_id',$method->id)->sum('total'),2)}} </span> €
                        </th>
                        <th></th>
                    </tr>
                </tfoot>


        </table>

    @endforeach

</div>
