<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-truck"></i> Route Info</h4>
    </div>
    <div class="panel-body">

        <div class="form-group{{ $errors->has('name') || $errors->has('name') ? ' has-error' : '' }} col-md-6">
            <float-label>
                {!! Form::text('name', NULL, ['class' => 'form-control', 'placeholder' => 'Name', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('name')" class="help-inline text-danger">@{{ errors.first('name') }}</div>
        </div>

        <div class="form-group{{ $errors->has('area_id') || $errors->has('area_id') ? ' has-error' : '' }} col-md-6">
            <float-label>
                {!! Form::select('area_id', $areas, NULL,
                ['class' => 'form-control select2', 'placeholder' => 'Area', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('acronym')" class="help-inline text-danger">@{{ errors.first('acronym') }}</div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-thumb-tack"></i> Pickup Points</h4>
    </div>
    <div class="panel-body">
        <div class="form-group{{ $errors->has('area_id') || $errors->has('area_id') ? ' has-error' : '' }} col-md-8">
            {!! Form::select(null
            , $pickupPoints, NULL,
            ['class' => 'form-control select2', 'placeholder' => 'Pickup Points', 'id' => 'pickupPoint']) !!}
        </div>

        <div class="col-md-4">
            <div class="btn btn-block btn-success disabled" id="addPoint">Add pickup point</div>
        </div>

        <div class="row">
            <div class="col-md-12" id="pointsList">
                @if(isset($route))
                    @foreach($route->pickupPoints as $pickupPoint)
                        @include('transport::partials.pickupPointLine', ['pickupPoint' => $pickupPoint])
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
      $('.select2').select2()

      $(document).ready(function () {
        $('#addPoint').click(function () {
          let pickupPoint = $('#pickupPoint').val()
          let count = $('.countPoints').length

          $.post('/addpoint/routes',
            {
              pickupPoint: pickupPoint,
              count: count + 1,
            })
            .done(function (data) {
              $('#pointsList').append(data)
            })
        })

        $('#pickupPoint').change(function() {
          $('#addPoint').removeClass('disabled');
        })

        $('body').on('click', '#deletePoint', function () {
          $(this).parent().parent().parent().remove()
        })
      })
    </script>
@endpush