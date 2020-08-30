<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-globe"></i> Area Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('name') || $errors->has('name') ? ' has-error' : '' }} col-md-8">
            <float-label>
                {!! Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Name', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('name')" class="help-inline text-danger">@{{ errors.first('name') }}</div>
        </div>

        <div class="form-group{{ $errors->has('acronym') || $errors->has('acronym') ? ' has-error' : '' }} col-md-4">
            <float-label>
                {!! Form::text('acronym', NULL, ['class' => 'form-control', 'placeholder' => 'Acronym', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('acronym')" class="help-inline text-danger">@{{ errors.first('acronym') }}</div>
        </div>
    </div>
</div>