<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-bus"></i> Transport</h4>
    </div>
    <div class="panel-body">

        <div class="col-md-5">
            <div class="form-group showSelector">
                {!! Form::select(null, [], null, ['id' => 'buses', 'class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="btn btn-success btn-block" id="addTransport"><i class="fa fa-plus"></i> Add Transport</div>
        </div>

        <div id="transportsList">
            @foreach($reservation->reservationTransports as $transport)
                @include('transport::partials.busLine', ['bus' => $transport->bus, 'transport' => $transport, 'id' => $loop->iteration])
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
      let $buses = $('#buses')
      let id = $('.countBuses').length + 1;

      $(document).ready(function () {
        $buses.select2({
          ajax: {
            url: '/list/buses',
            dataType: 'json',
            method: 'POST',
            delay: 250,

            data: function (params) {
              return {
                q: $('input[name="pass_id"]').val(),
                s: params.term
              }
            },
            processResults: function (data) {
              return {
                results: data
              }
            }
          },
          escapeMarkup: function (markup) { return markup }, // let our custom formatter work
          templateResult: returnTrans,
          templateSelection: returnTrans,
        })

        $('#passes').change(function () {
          $buses.removeAttr('disabled')

          $buses.select2('destroy')
          $buses.select2({
            ajax: {
              url: '/list/buses',
              dataType: 'json',
              method: 'POST',
              delay: 250,

              data: function (params) {
                return {
                  q: $('#passes').val(),
                  s: params.term
                }
              },
              processResults: function (data) {
                return {
                  results: data
                }
              }
            },
            escapeMarkup: function (markup) { return markup }, // let our custom formatter work
            templateResult: returnTrans,
            templateSelection: returnTrans,
          })
        })

        $('#addTransport').click(function () {
          let bus = $buses.val()

          $.post('/addtransport/reservations',
            {bus: bus, id: id}
          ).done(function (data) {
            $('#transportsList').append(data)
            id += 1;
          })
        })

        $('body').on('click', '#deleteBus', function() {
          $(this).parent().parent().parent().remove();
        })
      })

      function returnTrans (data) {
        if (data.loading)
          return data.text

        if (data.capacity)
          return '<b>Route:</b> ' + data.route.name + ' | <b>Bus:</b> ' + data.transporter.name + ' | <b>Capacity:</b> ' + (data.capacity - data.occupied) + '/' + data.capacity

        return ''
      }
    </script>
@endpush