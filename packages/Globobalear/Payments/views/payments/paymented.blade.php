
@if(!$reservation->payment_methods->isEmpty())
     <table class="table table-hover table-responsive">
        <thead>
        <tr class="title"><td colspan="5"><i class="fa fa-money"></i> Payments</td></tr>
        <tr>
            <td>Method</td>
            <td>Paid</td>
            <td></td>
        </tr>
        </thead>
        <tbody>


        @foreach($reservation->payment_methods as $payment)
            <tr>
                <td>
                <i class="fa fa-money"></i>
                    {{ $payment->name }}
                    @if($payment->id == 3)
                        - {{ $reservation->reference_number }}
                    @endif
                </td>
                <td><span class="price">{{ $payment->pivot->total }}</span> €</td>
                <td>
                <div style="padding: 0px 10px;" class="btn btn-danger pull-right clearfix">
                    <i class="fa fa-trash pointer removePayment" data-id="{{ $payment->pivot->id }}"></i>
                </div>
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th>Total</th>
            <th><span class="priceTotal">{{ $reservation->payment_methods->sum('pivot.total') }}</span> €</th>
            <th></th>
        </tr>
        </tfoot>
    </table>
@endif



@push('scripts')
<script>
    $(function () {
    $(document).on('click', ".removePayment", function (e) {

        e.preventDefault();
        var id = $(this).data('id');
        var reservationId = $('input[name="reservationId"]').val();

        swal({
            title: "REMOVE PAYMENT",
            text: "Type <b>REMOVE</b> to remove this payment",
            type: "input",
            html: true,
            cancelButtonText: 'Close',
            confirmButtonColor: "#ff1a1a",
            showCancelButton: true,
            closeOnConfirm: false,
            closeOnCancel: true,
            //animation: "slide-from-top",
            inputPlaceholder: "REMOVE"
        }, function (inputValue) {
            if (inputValue == "REMOVE") {
                swal({
                    title: "REASONS",
                    text: "Explain the reasons for the remove",
                    type: "input",
                    html: true,
                    cancelButtonText: 'Close',
                    confirmButtonColor: "#ff1a1a",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    inputPlaceholder: "Reasons",
                    showLoaderOnConfirm: true
                }, function (val) {
                    if (val.length > 4) {
                        $.post('/payments/removepayment', {id: id, reason: val,reservationId: reservationId}
                        ).done(function () {

                            swal("REMOVE", "Payment remove: " + id, "success");
                             window.location.replace('/reservations/'+ reservationId +'/payments');

                        });
                    } else {
                        swal.showInputError("You need to write something!");
                        return false;
                    }
                });
                return false;
            } else {
                swal.showInputError("You need to write CANCEL!");
                return false;
            }
        });
    });
});
</script>
@endpush
