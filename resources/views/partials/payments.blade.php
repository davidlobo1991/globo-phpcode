@if(!$reservation->payment_methods->isEmpty())

    <div class="panel panel-default">
    <div class="panel-heading">
    <h4> <i class="fa fa-money"></i> {{ trans('common.paymentmethod') }}</h4>
        </div>
    <div class="panel-body">

    <table class="table table-hover table-responsive">
        <thead>
        <tr class="title"><td colspan="5"><i class="fa fa-money" ></i> Payments</td></tr>
        <tr>
            <td>Method</td>
            <td>Paid</td>
            <td></td>
        </tr>
        </thead>

        <tbody>
        @foreach($reservation->payment_methods as $payment)
            <tr>
                <td><i class="fa fa-money"></i>
                    {{ $payment->name }}

                </td>
                <td><span class="price">{{ $payment->pivot->total }}</span> €</td>
                <td>
                    <i class="fa fa-trash pointer removePayment hidden" data-id="{{ $payment->pivot->id }}"></i>
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th><span class="text-success">Total</span></th>
            <th><span class="text-success">{{ $reservation->payment_methods->sum('pivot.total') }}</span> €</th>
            <th></th>
        </tr>
        </tfoot>
    </table>
    </div>
    </div>
@endif
