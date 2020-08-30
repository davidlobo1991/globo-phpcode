<div class="row js-show-container">

    <input type="hidden" name="ticket_type" value="{{ \Globobalear\Products\Models\TicketType::ADU }}">

    <div class="col-md-12 hidden" style="margin-bottom: 15px; margin-left: 15px;">
        <button class="btn btn-danger deleteLine" @if($numElemento < 1) disabled @endif>
            <i class="fa fa-times"></i>
        </button>
    </div>

    <div class="form-group col-md-5">
        <div class="col-md-12" >
            <div class="form-group">
                {!! Form::select('wristbands['. $numElemento .'][wristband_id]', $wristbands, null,
                    ['class' => 'form-control select js-show-select-'.$numElemento.' ennable-promocode wristbands' ,'v-validate' => "'required'",
                    'id' => 'wristbands'.$numElemento, 'placeholder' => 'Select a wristband', 'required' => true, 'form' => 'no-se-manda' ]) !!}
            </div>
            <input type="hidden" name="wristbands[]" class="wristbands-hidden">
        </div>
    </div>

    <div class="form-group col-md-5">
        <div class="col-md-12" >
            <div class="form-group">
                {!! Form::select('wristbands['. $numElemento .'][wristband_pass_id]', [], null,
                    ['class' => 'form-control select js-show-subselect','v-validate' => "'required'", 'id' => 'wristband_passes', 'disabled' => true, 'required' => true]) !!}
            </div>
        </div>
    </div>

    <div class="form-group col-md-2">
        <div class="col-md-12" >
            <div class="form-group">

                {!! Form::number('wristbands['. $numElemento .'][quantity]' , 0, ['class' => 'form-control quantity', 'required', 'disabled' => true, 'min' => 1]) !!}

            </div>
        </div>
    </div>

</div>
