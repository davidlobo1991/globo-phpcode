 
   <div class="row text-center status">
        <div class="col-md-6 bg-green" style="padding-top: 10px;">
            <p><b>TOTAL:</b> <span id="total"></span> {{ $reservation->getTotalWristbandsPriceAttribute() }}  €</p>
             <span id="toPay" data-pay="{{ $reservation->getTotalWristbandsPriceAttribute() }}"></span>
        </div>
        <div class="col-md-6 bg-red" style="padding-top: 10px;">
            <p><b>REMAINING:</b> <span id="remainder"> {{$reservation->remainderToPayWristband()}}</span>€ </p>
        </div>
    </div>
    {!! Form::open(['route' => 'payments.postPayments', 'method' => 'POST', "id"=>'default-form']) !!}

    {!! Form::hidden('reservationId', $reservation->id) !!}
    <hr>


        @if($paid == false)
             @foreach($paymentMethods as $paid)
             <div id="payments" class="form-group col-md-6">
                {!! Form::label('payment', $paid->name) !!}
                {!! Form::number('payment[' . $paid->id .']', 0, ['class' => 'form-control paymentInput payments','id'=>'payment[' . $paid->id .']','required' => true, 'readonly'=>false]) !!}
            </div>

           
            @endforeach
        @else
         <div class="pull-right col-md-3">
        {!! Form::label('') !!}
        <span class="btn btn-block btn-info" data-toggle="collapse" data-target="#paid">
            Payments
        </span>
        </div>
        <div class="col-md-12">
            <div id="paid" class="collapse in">
                <hr>
        @include('payments::payments.paymented')
        </div>
        </div>


        @endif
       <p id="validPayment"></p> 
        {!! Form::hidden('paidTotal', $reservation->payment_methods->sum('pivot.total'), ['id' => 'paidTotal']) !!}            
       



  <div class="col-md-12">
        <div class="box-footer with-border">
            <footer>
                {!! Form::submit(trans('common.save'), ['class' => 'btn btn-success', 'id' => 'sendPayments']) !!}
                 <a class="btn btn-primary"
                    href="{{ route($reservation->reservation_type_id.'.product', [$reservation->id])  }}">{{ trans('common.info') }}</a>
                <a class="btn btn-info"
                    href="{{ route($reservation->reservation_type_id.'.edit', [$reservation->id])  }}">{{ trans('common.edit') }}</a>
                <a class="btn btn-danger"
                    href="{{ route('reservations.index') }}">{{ trans('common.return') }}</a>
            </footer>
         </div>
 </div>
{!! Form::close() !!}

@push('scripts')
<script>
    $(function () {
        
        $('body').on('change', '.payments', function() {

               if($(this).val() == ""){
                    $(this).val(0);     
                }

                if ($(this).length) {
                   
                document.getElementById("validPayment").innerHTML = "";
                $(this).numeric({
                   
                    negative: false
                }, function () {
                    this.value = 0;
                    this.focus();
                    $(this).attr('style', 'border:5px solid red');
                    $(this).css("background-color", "#fef9cf");
                    document.getElementById("validPayment").innerHTML = "<div class='alert'>Positive integers only</div>";
                     
                });
            } else {
                
            }  
            
            refreshTotalPrice($(this));
            
        });

        function refreshTotalPrice($this)
        {
            var total = parseFloat($('#toPay').data('pay')).toFixed(2);
            var payed = parseFloat($('#paidTotal').val()).toFixed(2);

            var res = parseFloat(total - payed).toFixed(2);

            actual = getTotalPaid();

            if(res < actual) {
               
                 swal({
                        title: "PAYMENT",
                        text: "The total cost is " + res + " euros",
                        type: 'warning',
                        cancelButtonText: 'Close',
                        confirmButtonColor: "#5cb85c",
                        closeOnConfirm: false,
                        closeOnCancel: true,
                        animation: "slide-from-top",
                    });

                $this.val(0);

                actual = getTotalPaid()
            }

            var diff = parseFloat(res - actual);
            $('#remainder').html(diff.toFixed(2));
        }

        function getTotalPaid()
        {
            var actual = parseInt(0);

            $('input.payments').each(function(v, k) {
                actual += parseFloat($(this).val());
            });

            return actual;
        }

        $('#sendPayments').click(function (e) {
            e.preventDefault();

            var remainderToPay = parseFloat($('#remainder').html()).toFixed(2);

            if(remainderToPay > 0) {
                    swal({
                        title: "PAYMENT NOT COMPLETE",
                        text: "You must pay the full amount before continuing",
                        cancelButtonText: 'Close',
                        confirmButtonColor: "#5cb85c",
                        closeOnConfirm: false,
                        closeOnCancel: true,
                        animation: "slide-from-top",
                    });
                } else {
                    $('#default-form').submit();
                }
            })
});
</script>
@endpush
