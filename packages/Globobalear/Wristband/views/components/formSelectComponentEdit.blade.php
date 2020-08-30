<div class="row js-show-container">

    <input type="hidden" name="ticket_type" value="{{ $reservation->ticket_type or NULL }}">

@foreach($reservation->reservationWristbandPasses as $numElemento => $wrPass)
        <div class="col-md-12" style="margin-bottom: 15px; margin-left: 15px;">
            <button class="btn btn-danger deleteLine" disabled>
                <i class="fa fa-times"></i>
            </button>
        </div>

        <div class="form-group col-md-5">
            <div class="col-md-12" >
                <div class="form-group">
                    <select id="wristbands" class="form-control select" disabled="">
                        <option value="{{ $wrPass->wristband->id }}">{{ $wrPass->wristband->title }}</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group col-md-5">
            <div class="col-md-12" >
                <div class="form-group">
                    {!! Form::select('wristbands['. $numElemento .'][wristband_pass_id]', $wrPass->wristband->wristbandPasses->pluck('title', 'id') ?? null, $wrPass->id,
                        ['class' => 'form-control select js-show-subselect','v-validate' => "'required'", 'id' => 'wristband_passes', 'disabled' => false, 'required' => true]) !!}
                </div>
            </div>
        </div>

        <div class="form-group col-md-2">
            <div class="col-md-12" >
                <div class="form-group">

                    {!! Form::number('wristbands['. $numElemento .'][quantity]' , $wrPass->pivot->quantity, ['class' => 'form-control quantity', 'required', 'disabled' => true, 'min' => 1]) !!}

                </div>
            </div>
        </div>
    @endforeach
</div>