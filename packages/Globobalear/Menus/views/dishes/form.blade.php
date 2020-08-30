<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-cutlery"></i> Dish Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('name') || $errors->has('name') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Name', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('name')" class="help-inline text-danger">@{{ errors.first('name') }}</div>
        </div>

        <div class="form-group">
            @if (isset($dish))
                {!! Form::checkbox('vegetarian', 1, $dish->vegetarian, ['class' => 'iCheck']) !!}
            @else
                {!! Form::checkbox('vegetarian', 1, false, ['class' => 'iCheck']) !!}
            @endif
            Vegetarian
        </div>

        <div class="form-group{{ $errors->has('dishes_type') || $errors->has('dishes_type') ? ' has-error' : '' }}">
            <float-label>
                @if (!isset($dishesType->dishes_type_id))
                    {!! Form::select('dishes_type_id', $dishesType, null, ['class' => 'form-control select2', 'placeholder' => 'Dish type', 'v-validate' => "'required'"]) !!}
                @else
                    {!! Form::select('dishes_type_id', $dishesType, $dishes->dishes_type_id, ['class' => 'form-control select2', 'placeholder' => 'Dish type', 'v-validate' => "'required'"]) !!}
                @endif
            </float-label>
            <div v-if="errors.has('dishes_type')" class="help-inline text-danger">@{{ errors.first('dishes_type') }}
            </div>
        </div>

        <div class="form-group{{ $errors->has('description_allergens') || $errors->has('description_allergens') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::textarea('description_allergens ckeditor', NULL, ['class' => 'form-control', 'placeholder' => 'Description allergens', 'size' => '10x3']) !!}
            </float-label>
            <div v-if="errors.has('description_allergens ckeditor')" class="help-inline text-danger">@{{
                errors.first('description_allergens') }}
            </div>
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

      $('.select2').select2()
    </script>
@endpush
