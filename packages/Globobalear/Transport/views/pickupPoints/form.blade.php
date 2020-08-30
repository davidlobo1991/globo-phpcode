<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-map-marker"></i> Pickup Point Info</h4>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="form-group{{ $errors->has('name') || $errors->has('name') ? ' has-error' : '' }} col-md-6">
                <float-label>
                    {!! Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Name', 'v-validate' => "'required'"]) !!}
                </float-label>
                <div v-if="errors.has('name')" class="help-inline text-danger">@{{ errors.first('name') }}</div>
            </div>

            <div class="form-group{{ $errors->has('city_id') || $errors->has('city_id') ? ' has-error' : '' }} col-md-6">
                <float-label>
                    {!! Form::select('city_id', $cities, null, ['class' => 'form-control select2', 'placeholder' => 'City', 'v-validate' => "'required'"]) !!}
                </float-label>
                <div v-if="errors.has('city_id')" class="help-inline text-danger">@{{ errors.first('city_id') }}</div>
            </div>
        </div>

        
         <div class="row">
            <div class="col-md-12">
                <div id="map" style="height: 450px;"></div>
            </div>
        </div>
        
        <div class="row">
            {!! Form::hidden('longitude', null) !!}
            {!! Form::hidden('latitude', null) !!}

            <div class="form-group{{ $errors->has('mapaddress') || $errors->has('mapaddress') ? ' has-error' : '' }} col-md-12">
                <float-label>
                    {!! Form::text('mapaddress', null, ['class' => 'form-control', 'placeholder' => 'Map Address', 'v-validate' => "'required'", "readonly"=>'true']) !!}
                </float-label>
                <div v-if="errors.has('mapaddress')" class="help-inline text-danger">@{{ errors.first('mapaddress') }}</div>
            </div>
        </div>

       
    </div>
</div>

@push('scripts')
    <script type="text/javascript" src="{{ asset('js/internal/pickupMap.js') }}"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}"></script>

    <script>
      $('.select2').select2();
    </script>
@endpush