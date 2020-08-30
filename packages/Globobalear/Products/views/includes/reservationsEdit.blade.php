<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-ticket"></i> Pass Selection</h4>
    </div>
    <div class="panel-body">

        <div class="col-md-6">
            <div class="form-group showSelector">
                @if (null !== $reservation->pass_id)
                    {!! Form::hidden('product_id', $reservation->pass->product_id) !!}
                    {!! Form::text('products', $reservation->pass->product->name, ['id' => 'products', 'class' => 'form-control', 'readonly'=>true]) !!}
                @else
                    {!! Form::hidden('product_id', $reservation->product_id) !!}
                    {!! Form::text('products', $reservation->product->name, ['id' => 'products', 'class' => 'form-control', 'readonly'=>true]) !!}

                @endif

            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group passSelector">
                @if (null !== $reservation->pass_id)
                    {!! Form::hidden('pass_id', $reservation->pass_id) !!}
                    {!! Form::text('passes' , $reservation->pass->formattedDate, ['id' => 'passes', 'class' => 'form-control', 'readonly'=>true]) !!}
                @endif
            </div>
        </div>

        <div class="col-md-12" id="tableTickets">

        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        var passes = $('input[name="pass_id"]').val();

        if (passes) {
            $.post('/table/tickets',
                {
                    pass: passes,
                    reservation: $('input[name="reservation_id"]').val()
                })
                .done(function (data) {
                    $('#tableTickets').html(data)
                });
        } else {
            $.ajax({
                url: '/data/checkPasses',
                data: {
                    product_id: $('input[name="product_id"]').val(),
                    reservation: $('input[name="reservation_id"]').val()
                },
                method: 'GET',
            }).done(function (params) {
                if (params.status === false) {
                    $('#tableTickets').html(params.view);
                }
            }).fail(function (e) {
                console.log(e);
            });
        }
    </script>
@endpush
