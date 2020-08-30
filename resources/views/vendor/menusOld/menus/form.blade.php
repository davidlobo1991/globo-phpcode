<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-file-o"></i> Menu Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('name') || $errors->has('name') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Name', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('name')" class="help-inline text-danger">@{{ errors.first('name') }}</div>
        </div>

        <div class="form-group{{ $errors->has('dishes') || $errors->has('dishes') ? ' has-error' : '' }}">
            @if (isset($menu))
                {!! Form::select('dishes[]', $dish, $menu->dishes, ['class' => 'form-control', 'v-validate' => "'required'", 'id' => 'dishes-select2', 'multiple']) !!}
            @else
                {!! Form::select('dishes[]', $dish, NULL, ['class' => 'form-control', 'v-validate' => "'required'", 'id' => 'dishes-select2', 'multiple']) !!}
            @endif
            <div v-if="errors.has('dishes')" class="help-inline text-danger">@{{ errors.first('dishes') }}</div>
        </div>

    </div>
</div>

@push('scripts')
    <script type="text/javascript">
      $('#dishes-select2').select2({
        placeholder: 'Select a dish'
      })
    </script>
@endpush