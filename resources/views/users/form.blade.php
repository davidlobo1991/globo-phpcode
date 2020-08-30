<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-user"></i> Auth Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('name') || $errors->has('name') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Name', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('name')" class="help-inline text-danger">@{{ errors.first('name') }}</div>
        </div>

        <div class="form-group{{ $errors->has('email') || $errors->has('email') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('email', NULL, ['class' => 'form-control', 'placeholder' => 'Email', 'v-validate' => "'required|email'"]) !!}
            </float-label>
            <div v-if="errors.has('email')" class="help-inline text-danger">@{{ errors.first('email') }}</div>
        </div>

           <div class="form-group{{ $errors->has('role_id') || $errors->has('role_id') ? ' has-error' : '' }}">
           {!! Form::label('role_id', 'Role Types') !!}
            @if (isset($role))
                {!! Form::select('role_id', $role, null, ['class' => 'form-control select', 'v-validate' => "'required'", 'id' => 'role_id']) !!}
            @else
                {!! Form::select('role_id', $role, null, ['class' => 'form-control', 'v-validate' => "'required'", 'id' => 'role_id']) !!}
            @endif
            <div v-if="errors.has('role_id')" class="help-inline text-danger">@{{ errors.first('role_id') }}</div>
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

        
    </div>
</div>

<div class="panel panel-default" v-if="showPassword">
    <div class="panel-heading">
        <h4><i class="fa fa-key"></i> Password</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('password') || $errors->has('password') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Password', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('password')" class="help-inline text-danger">@{{ errors.first('password') }}</div>
        </div>

        <div class="form-group{{ $errors->has('password_confirmation') || $errors->has('password_confirmation') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirm Password', 'v-validate' => "'required|confirmed:password'"]) !!}
            </float-label>
            <div v-if="errors.has('password_confirmation')"
                 class="help-inline text-danger">@{{ errors.first('password_confirmation') }}</div>
        </div>
    </div>
</div>