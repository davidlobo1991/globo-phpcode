<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-calendar-o"></i> Wristband Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('title') || $errors->has('title') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'title*', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('title')" class="help-inline text-danger">@{{ errors.first('title') }}</div>
        </div>

        <div class="form-group{{ $errors->has('acronym') || $errors->has('acronym') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('acronym', NULL, ['class' => 'form-control', 'placeholder' => 'acronym*', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('acronym')" class="help-inline text-danger">@{{ errors.first('acronym') }}</div>
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
