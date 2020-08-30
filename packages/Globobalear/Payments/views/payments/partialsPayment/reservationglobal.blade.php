 <div class="col-md-12">
    <table class="table table-striped table-hover table-condensed table-responsive">
    <thead class="thead-default">
        <tr>
            <td class="col-lg-auto">Reservation </td>
            <td class="col-lg-auto">Phone </td>
            <td class="col-lg-auto">Type </td>
            <td class="col-lg-auto">{{ trans('common.show') }}</td>
            <td class="col-lg-auto">Paid </td>
            <td class="col-lg-auto">Revenue</td>
            <td class="col-lg-auto">Return </td>
            <td class="col-lg-auto">Commision </td>
            <td class="col-lg-auto">Date </td>
        </tr>
    </thead>
        <tbody>
        @foreach($payments as $paid)

                <tr>
                    <td><a href="{{ route($paid->reservation_type_id.'.show', $paid->reservation_id) }}">
                    {{ $paid->reservation_number }} - {{ $paid->customers_name }}
                    </a></td>
                    <td>{{ $paid->customers_phone }} </td>
                    <td>{{ $paid->type }} </td>
                    <td>{{ $paid->name_reservation }} </td>
                    <td>{{ $paid->total }} €</td>
                    <td>@if($paid->totalcomision){{ number_format ($paid->totalcomision, 2) }} € @else - @endif  </td>
                    <td>@if($paid->comisionpirates){{ number_format ($paid->comisionpirates,2) }} € @else - @endif </td>
                    <td>@if($paid->commission){{ $paid->commission }}% @else - @endif</td>
                    <td>{{ $paid->created_at->format('d-m-Y H:i') }} </td>


                </tr>

                <tr>
                    <td colspan="9">
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
    </tbody>
        <tfoot>
                <tr >
                <th colspan="4"></th>

                <th>Total:<span class="priceTotal"> {{number_format($payments->sum('total'),2)}} </span> €

                <th>Total:<span class="priceTotal"> {{number_format($payments->sum('totalcomision'),2)}} </span> €

                <th>Total:<span class="priceTotal"> {{number_format($payments->sum('comisionpirates'),2)}} </span> €
                </th>
                </th>
                <th></th>
            </tr>
        </tfoot>
    </table>

</div>
