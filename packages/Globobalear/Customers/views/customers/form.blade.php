<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-calendar-o"></i> Customer Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('name') || $errors->has('name') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Name*', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('name')" class="help-inline text-danger">@{{ errors.first('name') }}</div>
        </div>

        <div class="form-group{{ $errors->has('surname') || $errors->has('surname') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('surname', NULL, ['class' => 'form-control', 'placeholder' => 'surname*', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('surname')" class="help-inline text-danger">@{{ errors.first('surname') }}</div>
        </div>

        <div class="form-group{{ $errors->has('email') || $errors->has('email') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('email', NULL, ['class' => 'form-control', 'placeholder' => 'Email*', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('email')" class="help-inline text-danger">@{{ errors.first('email') }}</div>
        </div>

        <div class="form-group{{ $errors->has('birth_date') || $errors->has('birth_date') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('birth_date', NULL, ['id' => 'datepicker','class' => 'form-control', 'placeholder' => 'Birth date']) !!}
            </float-label>
            <div v-if="errors.has('birth_date')" class="help-inline text-danger">@{{ errors.first('birth_date') }}</div>
        </div>

        <div class="form-group{{ $errors->has('identification_number') || $errors->has('identification_number') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('identification_number', NULL, ['class' => 'form-control', 'placeholder' => 'Personal identification number']) !!}
            </float-label>
            <div v-if="errors.has('identification_number')"
                 class="help-inline text-danger">@{{ errors.first('identification_number') }}</div>
        </div>

        <div class="form-group{{ $errors->has('phone') || $errors->has('phone') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('phone', NULL, ['class' => 'form-control', 'placeholder' => 'Phone']) !!}
            </float-label>
            <div v-if="errors.has('phone')" class="help-inline text-danger">@{{ errors.first('phone') }}</div>
        </div>

        <div class="form-group{{ $errors->has('address') || $errors->has('address') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('address', NULL, ['class' => 'form-control', 'placeholder' => 'Address']) !!}
            </float-label>
            <div v-if="errors.has('address')" class="help-inline text-danger">@{{ errors.first('address') }}</div>
        </div>

        <div class="form-group{{ $errors->has('town') || $errors->has('town') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('town', NULL, ['class' => 'form-control', 'placeholder' => 'Town']) !!}
            </float-label>
            <div v-if="errors.has('town')" class="help-inline text-danger">@{{ errors.first('town') }}</div>
        </div>

        <div class="form-group{{ $errors->has('postal_code') || $errors->has('postal_code') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('postal_code', NULL, ['class' => 'form-control', 'placeholder' => 'Postal code']) !!}
            </float-label>
            <div v-if="errors.has('postal_code')"
                 class="help-inline text-danger">@{{ errors.first('postal_code') }}</div>
        </div>

        <div class="form-group{{ $errors->has('internal_comments') || $errors->has('internal_comments') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::textarea('internal_comments', NULL, ['class' => 'form-control', 'placeholder' => 'Internal comments', 'size' => '10x3']) !!}
            </float-label>
            <div v-if="errors.has('internal_comments')"
                 class="help-inline text-danger">@{{ errors.first('internal_comments') }}</div>
        </div>

        <div class="form-group{{ $errors->has('how_you_meet_us_name') || $errors->has('how_you_meet_us_name') ? ' has-error' : '' }}">
            <float-label>
                @if (!isset($customer->customers_how_you_meet_us_id))
                    {!! Form::select('customer_how_you_meet_us_id', $customershowyoumeetus, NULL, ['class' => 'form-control', 'placeholder' => 'How you meet us']) !!}
                @else
                    {!! Form::select('customer_how_you_meet_us_id', $customershowyoumeetus, $customer->customer_how_you_meet_us_id, ['class' => 'form-control', 'placeholder' => 'How you meet us']) !!}
                @endif
            </float-label>
            <div v-if="errors.has('how_you_meet_us_name')"
                 class="help-inline text-danger">@{{ errors.first('how_you_meet_us_name') }}</div>
        </div>

        <div class="form-group{{ $errors->has('gender_name') || $errors->has('gender_name') ? ' has-error' : '' }}">
            <float-label>
                @if (!isset($customer->gender_id))
                    {!! Form::select('gender_id', $genders, NULL, ['class' => 'form-control', 'placeholder' => 'Gender']) !!}
                @else
                    {!! Form::select('gender_id', $genders, $customer->gender_id, ['class' => 'form-control', 'placeholder' => 'Gender']) !!}
                @endif
            </float-label>
            <div v-if="errors.has('gender_name')"
                 class="help-inline text-danger">@{{ errors.first('gender_name') }}</div>
        </div>

        <div class="form-group{{ $errors->has('languages_id') || $errors->has('languages_id') ? ' has-error' : '' }}">
            <float-label>
                @if (!isset($customer->languages_id))
                    {!! Form::select('languages_id', $customerslanguages, NULL, ['class' => 'form-control', 'placeholder' => 'Language']) !!}
                @else
                    {!! Form::select('languages_id', $customerslanguages, $customer->languages_id, ['class' => 'form-control', 'placeholder' => 'Language']) !!}
                @endif
            </float-label>
            <div v-if="errors.has('languages_id')"
                 class="help-inline text-danger">@{{ errors.first('languages_id') }}</div>
        </div>

        <div class="form-group{{ $errors->has('newsletter') || $errors->has('newsletter') ? ' has-error' : '' }}">
            @if(isset($customer))
                {!! Form::checkbox('newsletter', 1, $customer->newsletter, ['class' => 'iCheck', 'placeholder' => 'Newsletter']) !!}
            @else
                {!! Form::checkbox('newsletter', 1, null, ['class' => 'iCheck', 'placeholder' => 'Newsletter']) !!}
            @endif
            Subscribed to newsletter
        </div>

        <div class="form-group{{ $errors->has('resident') || $errors->has('resident') ? ' has-error' : '' }}">
            @if(isset($customer))
                {!! Form::checkbox('resident', 1, $customer->resident, ['class' => 'iCheck', 'placeholder' => 'Resident']) !!}
            @else
                {!! Form::checkbox('resident', 1, null, ['class' => 'iCheck', 'placeholder' => 'Resident']) !!}
            @endif
            Is resident
        </div>

        <div class="form-group{{ $errors->has('is_enabled') || $errors->has('is_enabled') ? ' has-error' : '' }}">
            @if(isset($customer))
                {!! Form::checkbox('is_enabled', 1, $customer->is_enabled, ['class' => 'iCheck', 'placeholder' => 'Is enabled']) !!}
            @else
                {!! Form::checkbox('is_enabled', 1, 1, ['class' => 'iCheck', 'placeholder' => 'Is enabled']) !!}
            @endif
            Enabled
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
  //Date picker
  $('#datepicker').datepicker({
    autoclose: true,
    format: 'dd-mm-yyyy'
  })
</script>
@endpush
