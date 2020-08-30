<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-calendar-o"></i> Resseler Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('name') || $errors->has('name') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Name*', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('name')" class="help-inline text-danger">@{{ errors.first('name') }}</div>
        </div>

        <div class="form-group{{ $errors->has('email') || $errors->has('email') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('email', NULL, ['class' => 'form-control', 'placeholder' => 'Email*', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('email')" class="help-inline text-danger">@{{ errors.first('email') }}</div>
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

        <div class="form-group{{ $errors->has('city') || $errors->has('city') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('city', NULL, ['class' => 'form-control', 'placeholder' => 'City']) !!}
            </float-label>
            <div v-if="errors.has('city')" class="help-inline text-danger">@{{ errors.first('city') }}</div>
        </div>

        <div class="form-group{{ $errors->has('postal_code') || $errors->has('postal_code') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('postal_code', NULL, ['class' => 'form-control', 'placeholder' => 'Postal code']) !!}
            </float-label>
            <div v-if="errors.has('postal_code')"
                 class="help-inline text-danger">@{{ errors.first('postal_code') }}</div>
        </div>

         <div class="form-group{{ $errors->has('language_id') || $errors->has('language_id') ? ' has-error' : '' }}">
           {!! Form::label('language_id', 'Languages') !!}
            @if (isset($language))
                {!! Form::select('language_id', $language, null, ['class' => 'form-control select', 'v-validate' => "'required'", 'id' => 'language_id']) !!}
            @else
                {!! Form::select('language_id', $language, $language->language_id, ['class' => 'form-control', 'v-validate' => "'required'", 'id' => 'language_id']) !!}
            @endif
            <div v-if="errors.has('language_id')" class="help-inline text-danger">@{{ errors.first('language_id') }}</div>
        </div>


         <div class="form-group{{ $errors->has('country_id') || $errors->has('country_id') ? ' has-error' : '' }}">
           {!! Form::label('country_id', 'Countries') !!}
            @if (isset($countries))
                {!! Form::select('country_id', $countries, null, ['class' => 'form-control select', 'v-validate' => "'required'", 'id' => 'country_id']) !!}
            @else
                {!! Form::select('country_id', $countries, null, ['class' => 'form-control', 'v-validate' => "'required'", 'id' => 'country_id']) !!}
            @endif
            <div v-if="errors.has('country_id')" class="help-inline text-danger">@{{ errors.first('country_id') }}</div>
        </div>

        <div class="form-group{{ $errors->has('internal_comments') || $errors->has('internal_comments') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::textarea('internal_comments', NULL, ['class' => 'form-control', 'placeholder' => 'Internal comments', 'size' => '10x3']) !!}
            </float-label>
            <div v-if="errors.has('internal_comments')"
                 class="help-inline text-danger">@{{ errors.first('internal_comments') }}</div>
        </div>
    <div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-calendar-o"></i> Resellers Types</h4>
    </div>
    <div class="panel-body">

        
         <div class="form-group{{ $errors->has('company') || $errors->has('company') ? ' has-error' : '' }}">
            <float-label>
            {!! Form::label('company', 'Company: *') !!}
                {!! Form::text('company', NULL, ['class' => 'form-control', 'placeholder' => 'Company*', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('company')" class="help-inline text-danger">@{{ errors.first('company') }}</div>
        </div>


         <div class="form-group{{ $errors->has('discount') || $errors->has('discount') ? ' has-error' : '' }}">
            <float-label>
            {!! Form::label('area', 'Discount %: *') !!}
                {!! Form::number('discount', NULL, ['class' => 'form-control','v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('discount')" class="help-inline text-danger">@{{ errors.first('discount') }}</div>
        </div>

        <div class="form-group{{ $errors->has('user_id') || $errors->has('user_id') ? ' has-error' : '' }}">
           {!! Form::label('area', 'Manager') !!}
            @if (isset($user))
                {!! Form::select('user_id', $user, null, ['class' => 'form-control select',  'id' => 'user_id']) !!}
            @else
                {!! Form::select('user_id', $user, null, ['class' => 'form-control', 'id' => 'user_id']) !!}
            @endif
            <div v-if="errors.has('user_id')" class="help-inline text-danger">@{{ errors.first('user_id') }}</div>
        </div>


       <div class="form-group{{ $errors->has('resellers_type_id') || $errors->has('resellers_type_id') ? ' has-error' : '' }}">
           {!! Form::label('resellers_type_id', 'Resellers Types') !!}
            @if (isset($resellerType))
                {!! Form::select('resellers_type_id', $resellerType, null, ['class' => 'form-control select', 'v-validate' => "'required'", 'id' => 'resellers_type_id']) !!}
            @else
                {!! Form::select('resellers_type_id', $resellerType, null, ['class' => 'form-control', 'v-validate' => "'required'", 'id' => 'resellers_type_id']) !!}
            @endif
            <div v-if="errors.has('resellers_type_id')" class="help-inline text-danger">@{{ errors.first('resellers_type_id') }}</div>
        </div>

         <div class="form-group{{ $errors->has('agent_type_id') || $errors->has('agent_type_id') ? ' has-error' : '' }}">
           {!! Form::label('agent_type_id', 'Agent types') !!}
            @if (isset($agenttypes))
                {!! Form::select('agent_type_id', $agenttypes, null, ['class' => 'form-control select', 'id' => 'agent_type_id']) !!}
            @else
                {!! Form::select('agent_type_id', $agenttypes, null, ['class' => 'form-control', 'id' => 'agent_type_id']) !!}
            @endif
            <div v-if="errors.has('agent_type_id')" class="help-inline text-danger">@{{ errors.first('agent_type_id') }}</div>
        </div>

         <div class="form-group{{ $errors->has('area_id') || $errors->has('area_id') ? ' has-error' : '' }}">
           {!! Form::label('area', 'Area') !!}
            @if (isset($area))
                {!! Form::select('area_id', $area, null, ['class' => 'form-control select', 'id' => 'area_id']) !!}
            @else
                {!! Form::select('area_id', $area, null, ['class' => 'form-control', 'id' => 'area_id']) !!}
            @endif
            <div v-if="errors.has('area_id')" class="help-inline text-danger">@{{ errors.first('area_id') }}</div>
        </div>



        </div>
    </div>

        <div class="form-group{{ $errors->has('is_enabled') || $errors->has('is_enabled') ? ' has-error' : '' }}">
            @if(isset($resellers))
              {!! Form::checkbox('is_enabled',$resellers->is_enable, $resellers->is_enable, ['class' => 'iCheck', 'placeholder' => 'Is Enabled']) !!}
            @else
                {!! Form::checkbox('is_enabled', 0, 0, ['class' => 'iCheck', 'placeholder' => 'Is Enabled']) !!}
            @endif
            Is Enabled
        </div>



    </div>
</div>

@push('scripts')
    <script type="text/javascript">
      $('select.select').select2({
        placeholder: 'Select a:'
      })
    </script>
@endpush
