<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-truck"></i> Transporter Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('title') || $errors->has('title') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Name', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('title')" class="help-inline text-danger">@{{ errors.first('title') }}</div>
        </div>
    </div>
</div>