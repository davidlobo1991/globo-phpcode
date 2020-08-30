<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-percent"></i> Promocode Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('code') || $errors->has('code') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('code', NULL, ['class' => 'form-control', 'placeholder' => 'Code', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('code')" class="help-inline text-danger">@{{ errors.first('code') }}</div>
        </div>

        <div class="form-group{{ $errors->has('discount') || $errors->has('discount') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::number('discount', NULL, ['max' => 100, 'min' => 0, 'class' => 'form-control',
                    'placeholder' => 'Discount (%)', 'v-validate' => "'required|numeric|between:0,100'"]) !!}
            </float-label>
            <div v-if="errors.has('discount')" class="help-inline text-danger">@{{ errors.first('discount') }}</div>
        </div>

        <div class="form-group{{ $errors->has('valid_from') || $errors->has('valid_from') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('valid_from', isset($promocode->valid_from) ? $promocode->valid_from->format('d/m/Y') : ''
                    , ['class' => 'form-control valid_from', 'placeholder' => 'Valid from']) !!}
            </float-label>
            <div v-if="errors.has('valid_from')" class="help-inline text-danger">@{{ errors.first('valid_from') }}</div>
        </div>

        <div class="form-group{{ $errors->has('valid_to') || $errors->has('valid_to') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('valid_to', isset($promocode->valid_to) ? $promocode->valid_to->format('d/m/Y') : ''
                    , ['class' => 'form-control valid_to', 'placeholder' => 'Valid to']) !!}
            </float-label>
            <div v-if="errors.has('valid_to')" class="help-inline text-danger">@{{ errors.first('valid_to') }}</div>
        </div>

        <div class="form-group{{ $errors->has('for_from') || $errors->has('for_from') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('for_from', isset($promocode->for_from) ? $promocode->for_from->format('d/m/Y') : ''
                    , ['class' => 'form-control for_from', 'placeholder' => 'For from']) !!}
            </float-label>
            <div v-if="errors.has('for_from')" class="help-inline text-danger">@{{ errors.first('for_from') }}</div>
        </div>

        <div class="form-group{{ $errors->has('for_to') || $errors->has('for_to') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('for_to', isset($promocode->for_to) ? $promocode->for_to->format('d/m/Y') : ''
                    , ['class' => 'form-control for_to', 'placeholder' => 'For to']) !!}
            </float-label>
            <div v-if="errors.has('for_to')" class="help-inline text-danger">@{{ errors.first('for_to') }}</div>
        </div>

        <div class="form-group{{ $errors->has('take_place') || $errors->has('take_place') ? ' has-error' : '' }}">
            {!! Form::checkbox('single_use', 1, null, ['class' => 'iCheck']) !!}
            Single Use
        </div>

        <div class="form-group{{ $errors->has('take_place') || $errors->has('take_place') ? ' has-error' : '' }}">
            {!! Form::checkbox('canceled', 1, null, ['class' => 'iCheck']) !!}
            Canceled
        </div>

        <div class="form-group{{ $errors->has('products[]') || $errors->has('products[]') ? ' has-error' : '' }}">
            {{  Form::label('products', 'Select products') }}
            <float-label>
                {!! Form::select('products[]', $products, isset($selectedProducts) ? $selectedProducts : null, ['class' => 'form-control selectwo',
                    'multiple' => 'true']) !!}
            </float-label>
            <div v-if="errors.has('shows[]')" class="help-inline text-danger">@{{ errors.first('products[]') }}</div>
        </div>

        <div class="form-group{{ $errors->has('packs[]') || $errors->has('packs[]') ? ' has-error' : '' }}">
            {{  Form::label('packs', 'Select packs') }}
            <float-label>
                {!! Form::select('packs[]', $packs, isset($$selectedPacks) ? $$selectedPacks : null, ['class' => 'form-control selectwo',
                    'multiple' => 'true']) !!}
            </float-label>
            <div v-if="errors.has('packs[]')" class="help-inline text-danger">@{{ errors.first('packs[]') }}</div>
        </div>

        <div class="form-group{{ $errors->has('wristbands[]') || $errors->has('wristbands[]') ? ' has-error' : '' }}">
            {{  Form::label('wristbands', 'Select wristbands') }}
            <float-label>
                {!! Form::select('wristbands[]', $wristbands, isset($$selectedWristbands) ? $$selectedWristbands : null, ['class' => 'form-control selectwo',
                    'multiple' => 'true']) !!}
            </float-label>
            <div v-if="errors.has('wristbands[]')" class="help-inline text-danger">@{{ errors.first('wristbands[]') }}</div>
        </div>

        {{--  <div class="form-group{{ $errors->has('shows_globo_balear') || $errors->has('shows_globo_balear') ? ' has-error' : '' }}">
            <float-label>
            <select name="shows_globo_balear[]" class="form-control select2" multiple='multiple'>
                @foreach($shows as $show)
                    <option value="{{ $show->id }}" @if( isset($wristbandPass) and \App\Helpers\Functions::inArray($wristbandPass->shows, $show->id)) selected @endif>{{ $show->name }}</option>
                @endforeach
            </select>
            </float-label>
            <div v-if="errors.has('shows_globo_balear')" class="help-inline text-danger">@{{ errors.first('shows_globo_balear') }}</div>
        </div>  --}}

    </div>
</div>

@push('scripts')
<script>
  $('.valid_from').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'dd/mm/yyyy'
  }).on('changeDate', function (selected) {
    let minDate = new Date(selected.date.valueOf())
    $('.valid_to').datepicker('setStartDate', minDate)
  })

  $('.valid_to').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'dd/mm/yyyy'
  }).on('changeDate', function (selected) {
    let maxDate = new Date(selected.date.valueOf())
    $('.valid_from').datepicker('setEndDate', maxDate)
  })

  $('.for_from').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'dd/mm/yyyy'
  }).on('changeDate', function (selected) {
    let minDate = new Date(selected.date.valueOf())
    $('.for_to').datepicker('setStartDate', minDate)
  })

  $('.for_to').datepicker({
    autoclose: true,
    todayHighlight: true,
    format: 'dd/mm/yyyy'
  }).on('changeDate', function (selected) {
    let maxDate = new Date(selected.date.valueOf())
    $('.for_from').datepicker('setEndDate', maxDate)
  });

  $('.selectwo').select2();
</script>
@endpush
