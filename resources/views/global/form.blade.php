<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-user"></i> Global Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('booking_fee') || $errors->has('booking_fee') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::number('booking_fee', $global->booking_fee, ['id'=>'booking_fee','class' => 'form-control', 'placeholder' => 'Booking Fee', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('booking_fee')" class="help-inline text-danger">@{{ errors.first('name') }}</div>
        </div>

        <div class="form-group{{ $errors->has('paypal') || $errors->has('paypal') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::number('paypal', $global->paypal, ['id'=>'paypal','class' => 'form-control', 'placeholder' => 'Paypal', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('paypal')" class="help-inline text-danger">@{{ errors.first('paypal') }}</div>
        </div>

      
        
    </div>



