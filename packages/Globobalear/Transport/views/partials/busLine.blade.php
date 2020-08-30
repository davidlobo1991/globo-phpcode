<div class="countBuses col-md-12" id="bus{{ $id }}">
    <hr>

    <div class="row">
        <div class="form-group col-md-12">
            {!! Form::hidden("transport[{$id}][bus_id]", $bus->id, ['class' => 'busId']) !!}

            <b>Route: </b> {{ $bus->route->name }} | <b>Bus:</b> {{ $bus->transporter->name }} |
            <b>Capacity:</b> {{ $bus->capacity - $bus->occupied }}/{{ $bus->capacity }}
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-7">
            @if(isset($transport))
                <p style="line-height: 34px;">{{ $transport->pickup_point }} | <b>Price: </b> {{ $transport->price }} |
                    <b>Hour:</b> {{ $transport->hour }}</p>
                {!! Form::hidden("transport[{$id}][pickup_point_id]", $transport->pickup_point_id) !!}
            @else
                {!! Form::select("transport[{$id}][pickup_point_id]", [], null, ['class' => 'form-control pickupPoint']) !!}
            @endif
        </div>

        <div class="col-md-3">
            @if(isset($transport))
                {!! Form::number("transport[{$id}][quantity]", $transport->quantity, ['class' => 'form-control quantity', 'placeholder' => 'Quantity', 'min' => '0']) !!}
            @else
                {!! Form::number("transport[{$id}][quantity]", null, ['class' => 'form-control quantity', 'placeholder' => 'Quantity', 'min'=> '0']) !!}
            @endif
        </div>

        <div class="form-group col-md-2">
            <div class="btn btn-block btn-danger" id="deleteBus">
                <i class="fa fa-trash"></i>
            </div>
        </div>
    </div>

    <div class="row infoPickup"></div>

</div>

@push('scripts')
<script type="text/javascript">
  $(document).ready(function () {
    let id = '{{ $id }}'

    $('#bus' + id + ' .pickupPoint').select2({
      ajax: {
        url: '/list/pickupPoints',
        dataType: 'json',
        method: 'POST',
        delay: 250,

        data: function (params) {
          return {
            q: $('.busId').val(),
            s: params.term
          }
        },
        processResults: function (data) {
          return {
            results: data
          }
        }
      },
      escapeMarkup: function (markup) { return markup },
      templateResult: returnPick,
      templateSelection: returnPick,
    })
  })

  function returnPick (data) {
    if (data.loading)
      return data.text

    if (data.name)
      return data.name + ' | <b>Price:</b> ' + data.pivot.price + ' | <b>Hour:</b> ' + data.pivot.hour

    return ''
  }

   //Límites para transporte / buses.
    $('body').on('change', '#transportsList .quantity', function () {
        var $this = $(this);
        var val = total = 0;

        $('#transportsList .quantity').each(function (k, v) {
            val += parseInt($(this).val());
        });
    
        $('input.seat-ticket-price').each(function (k, v) {
            total += parseInt($(this).val());
        });

        if (total < val) {
           
             swal({
                        title: "There are only " + total + " seats availables",
                        confirmButtonColor: "#d9534f",
                        closeOnConfirm: true,
                        animation: "slide-from-top"
                    });
            $this.val(0);
        }
    });
    </script>
@endpush