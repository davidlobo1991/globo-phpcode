<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-bus"></i> Transport</h4>
    </div>
    <div class="panel-body">

        <div class="col-md-5">
            <div class="form-group showSelector">
                {!! Form::select(null, [], null, ['id' => 'buses', 'class' => 'form-control', 'disabled']) !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="btn btn-success btn-block" id="addTransport"><i class="fa fa-plus"></i> Add Transport</div>
        </div>

        <div id="transportsList">

        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
      let $buses = $('#buses')
      let id_transport = 1;

      $(document).ready(function () {
        $buses.select2()

        $passes.change(function () {
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
              processResults: function (datatransport) {
                return {
                  results: datatransport
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
            {bus: bus, id: id_transport}
          ).done(function (datatransport) {
            $('#transportsList').append(datatransport)
            id_transport += 1;
          })
        })

        $('body').on('click', '#deleteBus', function() {
          $(this).parent().parent().parent().remove();
        })
      })

      function returnTrans (datatransport) {
        if (datatransport.loading)
          return datatransport.text

        if (datatransport.capacity)
          return '<b>Route:</b> ' + datatransport.route.name + ' | <b>Bus:</b> ' + datatransport.transporter.name + ' | <b>Capacity:</b> ' + (datatransport.capacity - datatransport.occupied) + '/' + datatransport.capacity

        return ''
      }

    </script>
@endpush