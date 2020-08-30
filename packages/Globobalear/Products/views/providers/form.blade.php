<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-user"></i> Provider Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group {{ $errors->has('name') || $errors->has('name') ? 'has-error' : '' }}">
            <float-label>
                {!! Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Name', 'v-validate' => "'required|max:255'"]) !!}
            </float-label>
            <div v-if="errors.has('name')" class="help-inline text-danger">@{{ errors.first('name') }}</div>
        </div>
        <div class="form-group {{ $errors->has('email') || $errors->has('email') ? 'has-error' : '' }}">
            <float-label>
                {!! Form::email('email', NULL, ['class' => 'form-control', 'placeholder' => 'Email', 'v-validate' => "'email|max:255'"]) !!}
            </float-label>
            <div v-if="errors.has('email')" class="help-inline text-danger">
                @{{ errors.first('email') }}
            </div>
        </div>
    </div>
</div>
