<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-calendar-o"></i> Wristband Info</h4>
    </div>
    <div class="panel-body">

        <div class="row">
            {{--  //TODO quitar title y shows de pirates  --}}
              <div class="col-md-12">
                <div class="form-group{{ $errors->has('title') || $errors->has('title') ? ' has-error' : '' }}">
                    <float-label>
                        {!! Form::text('title', null, ['class' => 'form-control', 'placeholder' => 'Title*', 'v-validate' => "'required'"]) !!}
                    </float-label>
                    <div v-if="errors.has('title')" class="help-inline text-danger">@{{ errors.first('title') }}</div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group{{ $errors->has('wristband_id') || $errors->has('wristband_id') ? ' has-error' : '' }}">
                    <float-label>
                        {!! Form::select('wristband_id', $wristband, NULL, ['class' => 'form-control', 'v-validate' => "'required'"]) !!}
                    </float-label>
                    <div v-if="errors.has('wristband_id')" class="help-inline text-danger">@{{ errors.first('wristband_id') }}</div>
                </div>
            </div>

            <div class="col-md-12">
                {{-- //TODO rango solo es de un mes, no puedes ir al año ni al mes coño --}}
                <div class="form-group input-group" id="'event_period">
                    <float-label>
                        {!! Form::text('date_start', NULL, ['class' => 'form-control br-r-0 actual_range', 'placeholder' => 'Date start*', 'v-validate' => "'required'"]) !!}
                    </float-label>
                    <div v-if="errors.has('date_start')" class="help-inline text-danger">@{{ errors.first('date_start') }}</div>

                    <div class="input-group-addon br-0">to</div>

                    <float-label>
                        {!! Form::text('date_end', NULL, ['class' => 'form-control br-l-0 actual_range', 'placeholder' => 'Date end*', 'v-validate' => "'required'"]) !!}
                    </float-label>
                    <div v-if="errors.has('date_end')" class="help-inline text-danger">@{{ errors.first('date_end') }}</div>
                </div>
                <div id="event_period" class="hidden">
                    <input type="text" class="form-control actual_range" style="width: 50%;">
                    <input type="text" class="form-control actual_range" style="width: 50%;">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group{{ $errors->has('price') || $errors->has('price') ? ' has-error' : '' }}">
                    <float-label>
                        {!! Form::number('price', NULL, ['class' => 'form-control', 'placeholder' => 'Price*', 'v-validate' => "'required'", 'step' => '0.01']) !!}
                    </float-label>
                    <div v-if="errors.has('price')" class="help-inline text-danger  z-index-999">@{{ errors.first('price') }}</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group{{ $errors->has('quantity') || $errors->has('quantity') ? ' has-error' : '' }}">
                    <float-label>
                        {!! Form::number('quantity', NULL, ['class' => 'form-control' , 'placeholder' => 'Quantity*', 'v-validate' => "'required'"]) !!}
                    </float-label>
                    <div v-if="errors.has('quantity')" class="help-inline text-danger z-index-999">@{{ errors.first('quantity') }}</div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group{{ $errors->has('products_globo_balear') || $errors->has('products_globo_balear') ? ' has-error' : '' }}">
                    <float-label>
                        <select name="products_globo_balear[]" class="form-control select2" multiple='multiple'>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" @if( isset($wristbandPass) and \App\Helpers\Functions::inArray($wristbandPass->products, $product->id)) selected @endif>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </float-label>
                    <div v-if="errors.has('products_globo_balear')" class="help-inline text-danger">@{{ errors.first('products_globo_balear') }}</div>
                </div>
            </div>

            {{--
            //Shows
            <div class="col-md-12">
                <div class="form-group{{ $errors->has('title') || $errors->has('title') ? ' has-error' : '' }}">
                    <float-label>
                    <select name="shows_pirates[]" class="form-control select2" multiple='multiple'>
                        @foreach($showPirates as $showPirate)
                            <option value="{{ $showPirate->id }}" @if( isset($wristbandPass) and \App\Helpers\Functions::inArray($wristbandPass->showsPirates, $showPirate->id)) selected @endif>{{ $showPirate->title }}</option>
                        @endforeach
                    </select>
                    </float-label>
                    <div v-if="errors.has('shows_globo_balear')" class="help-inline text-danger">@{{ errors.first('shows_globo_balear') }}</div>
                </div>
            </div>
            --}}

        </div>


    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        $('select.select2').select2({
            placeholder: 'Select a:'
        })

        $('#event_period').datepicker({
            inputs: $('.actual_range'),
            forceParse: false,
            format: "dd-mm-yyyy",
            todayHighlight: true,
            clearBtn: true,
        });

    </script>
@endpush
