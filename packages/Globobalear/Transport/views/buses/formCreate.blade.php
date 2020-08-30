@push('styles')
    <link rel="stylesheet" href="{{ asset('plugins/duallistbox/duallistbox.min.css') }}">
@endpush

<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-bus"></i> Bus Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('capacity') || $errors->has('capacity') ? ' has-error' : '' }} col-md-4">
            <float-label>
                {!! Form::number('capacity', NULL, ['class' => 'form-control', 'placeholder' => 'Capacity', 'v-validate' => "'required|numeric'"]) !!}
            </float-label>
            <div v-if="errors.has('capacity')" class="help-inline text-danger">@{{ errors.first('capacity') }}</div>
        </div>

        <div class="form-group{{ $errors->has('transporter_id') || $errors->has('transporter_id') ? ' has-error' : '' }} col-md-4">
            <float-label>
                {!! Form::select('transporter_id', $transporters, NULL, ['class' => 'form-control select2', 'placeholder' => 'Transporter']) !!}
            </float-label>
        </div>

        <div class="form-group{{ $errors->has('route_id') || $errors->has('route_id') ? ' has-error' : '' }} col-md-4">
            <float-label>
                {!! Form::select('route_id', $routes, null, ['class' => 'form-control select2', 'placeholder' => 'Route']) !!}
            </float-label>
        </div>

        <div class="row">
            <div class="form-group col-md-12">
                {!! Form::select('pass_id[]', $passes, null, ['id' => 'dual', 'multiple' => 'multiple']) !!}
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type='text/javascript' src='{{ asset('plugins/duallistbox/duallistbox.min.js') }}'></script>
    <script>
      $('#dual').bootstrapDualListbox({
        nonSelectedListLabel: 'Non-selected Passes',
        selectedListLabel: 'Selected Passes',
        preserveSelectionOnMove: 'moved',
        moveOnSelect: false,
      })

      $('.select2').select2()
    </script>
@endpush