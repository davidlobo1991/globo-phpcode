<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-tag"></i> Promocode</h4>
    </div>
    <div class="panel-body">
        <div class="form-group col-md-4">
            <float-label>

                {!! Form::text('promocode', $reservation->promocode->code ?? null,
                    ['id' => 'promocode', 'class' => 'form-control', 'placeholder' => 'Promocode', 'disabled']) !!}

            </float-label>
        </div>
        <div class="form-group col-md-2">
            <button class="btn btn-block btn-success @if(!isset($reservation) || (!$reservation->promocode)) disabled @endif"
                    id="tryPromocode">Try it
            </button>
        </div>
        <div class="form-group col-md-6 text-center" id="promocodeMessage" style="margin-bottom: 15px;">
            @if(isset($reservation) && ($reservation->promocode))
                <p class="text-success text-bold" style="line-height: 34px;">
                    Promocode applied, {{ $reservation->promocode->discount }}% discount.
                </p>
                {!! Form::hidden('promocode_id', $reservation->promocode->id) !!}
                {!! Form::hidden('discount', $reservation->promocode->discount) !!}
            @endif
        </div>
    </div>
</div>


@push('scripts')
    <script>
        $(document).ready(function () {
            let $promocodeInput = $('#promocode')
            let $promocodeButton = $('#tryPromocode')

            $('#passes').change(function () {
                if ($(this).val()) {
                    $promocodeInput.removeAttr('disabled')
                    $promocodeButton.removeClass('disabled')
                }
            })

            /**
             * Add 'ennable-promocode' class to the select for remove disabled
             */
            $('body').on('change', '.ennable-promocode', function () {
                $promocodeInput.removeAttr('disabled')
                $promocodeButton.removeClass('disabled')
            })

            $('#pack').change(function () {
                if ($(this).val()) {
                    $promocodeInput.removeAttr('disabled')
                    $promocodeButton.removeClass('disabled')
                }
            })

            //Adaptación a packs y wristbands
            $promocodeButton.click(function (e) {
                checkIfPromocodeIsNull();
                e.preventDefault()
                $.post('{{ route('check-promocode') }}',
                    $('#mainForm').find(`
                        input[name=promocode],
                        input[name=reservation_type_id],
                        select[name=pass_id],
                        select[name=pack],
                        .wristbands,
                        select.js-show-subselect
                    `).serialize()

                ).done(function (data) {
                    $('#promocodeMessage').html(data)
                })
            })

            function checkIfPromocodeIsNull() {
                let $code = $('input[name=promocode]');
                let $codeVal = $code.val();
                if(! $codeVal){
                    swal('Insert Promocode!')
                    return false;
                }
            }
        })
    </script>
@endpush
