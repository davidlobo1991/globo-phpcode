<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-leanpub"></i> Carte Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('name') || $errors->has('name') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Name', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('name')" class="help-inline text-danger">@{{ errors.first('name') }}</div>
        </div>

        <div class="form-group{{ $errors->has('menus') || $errors->has('menus') ? ' has-error' : '' }}">
            @if (isset($carte))
                {!! Form::select('menus[]', $menus, $carte->menus, ['class' => 'form-control', 'v-validate' => "'required'", 'id' => 'menus-select2', 'multiple']) !!}
            @else
                {!! Form::select('menus[]', $menus, NULL, ['class' => 'form-control', 'v-validate' => "'required'", 'id' => 'menus-select2', 'multiple']) !!}
            @endif
            <div v-if="errors.has('menus')" class="help-inline text-danger">@{{ errors.first('menus') }}</div>
        </div>

         <div class="form-group{{ $errors->has('products') || $errors->has('shows') ? ' has-error' : '' }}">
            @if (isset($products))
                {!! Form::select('product_id', $products, null, ['class' => 'form-control', 'v-validate' => "'required'", 'id' => 'product_id']) !!}
            @else
                {!! Form::select('product_id', $menus, null, ['class' => 'form-control', 'v-validate' => "'required'", 'id' => 'product_id']) !!}
            @endif
            <div v-if="errors.has('products')" class="help-inline text-danger">@{{ errors.first('products') }}</div>
        </div>

        <div class="form-group{{ $errors->has('seat_types') || $errors->has('seat_types') ? ' has-error' : '' }}">
            @if (isset($seatType))
                {!! Form::select('seat_type_id', $seatType, null, ['class' => 'form-control', 'v-validate' => "'required'", 'id' => 'seat_type_id']) !!}
            @else
                {!! Form::select('seat_type_id', $menus, NULL, ['class' => 'form-control', 'v-validate' => "'required'", 'id' => 'seat_type_id']) !!}
            @endif
            <div v-if="errors.has('seat_types')" class="help-inline text-danger">@{{ errors.first('seat_types') }}</div>
        </div>

        <div class="form-group">
            @if (isset($carte))
                {!! Form::checkbox('is_enable', 1, $carte->is_enable, ['class' => 'iCheck', 'placeholder' => 'Enable']) !!}
            @else
                {!! Form::checkbox('is_enable', 1, true, ['class' => 'iCheck', 'placeholder' => 'Enable']) !!}
            @endif
            Enable
        </div>

    </div>
</div>

@push('scripts')
    <script type="text/javascript">
      $('#menus-select2').select2({
        placeholder: 'Select a menu'
      })
    </script>
@endpush
