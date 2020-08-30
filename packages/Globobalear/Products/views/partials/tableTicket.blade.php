<table id="ticketTable" class="table table-responsive table-condensed text-center">
    <thead>
    <tr>
        <th class="empty col-md-2"></th>
        @foreach($ticketTypes as $ticketType)
            <th>{{ $ticketType->title }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($seatTypes as $seatType)
        <tr class="@include('products::partials.amberTrigger')">
            <td class="seatType">
                {{ $seatType->title }}
                @if (isset($product) && $product->has_passes)
                    <p>({{ $seatType->pivot->freeSeats }}/{{ $seatType->pivot->seats_available }})</p>
                @endif
              
            </td>
           
            @foreach($ticketTypes as $ticketType)
                <td>
                    <span class="price">

                         @if (isset($product) && $product->has_passes)
                            {{ $seatType->pivot->ticketTypes->where('id', $ticketType->id)->first()->pivot->price }} €
                            {!! Form::hidden("data[{$seatType->id}][{$ticketType->id}][price]", $seatType->pivot->ticketTypes->where('id', $ticketType->id)->first()->pivot->price) !!}
                       @else
                             @if ($product->prices->where('ticket_type_id', $ticketType->id)->where('seat_type_id', $seatType->id)->first())
                                {{ $product->prices->where('ticket_type_id', $ticketType->id)->where('seat_type_id', $seatType->id)->first()->price }} €
                                {!! Form::hidden("data[{$seatType->id}][{$ticketType->id}][price]",  $product->prices->where('ticket_type_id', $ticketType->id)->where('seat_type_id', $seatType->id)->first()->price) !!}
                            @else
                                 Price not selected <br/>
                                 0 €
                                {!! Form::hidden("data[{$seatType->id}][{$ticketType->id}][price]", 0) !!}

                            @endif
                        @endif
                    </span>
                    <div class="form-group">
                        @if(is_null($reservation))
                            {!! Form::number("data[{$seatType->id}][{$ticketType->id}][qty]",0,
                            ['class' => $ticketType->id <> 3 ? 'form-control seat-ticket-price takePlace' : 'form-control seat-ticket-price', 'min' => '0','max' => $seatType->pivot->freeSeats,
                            'data-seattype' => $seatType->id,
                            'data-max' => $seatType->pivot->freeSeats,
                            'data-type' => $seatType->id,
                            'data-ticket' => $ticketType->id
                            
                            ]
                                 
                            ) !!}
                        @else
                            {!! Form::number("data[{$seatType->id}][{$ticketType->id}][qty]",
                                $reservation->getSeatsQuantity($seatType, $ticketType),
                                ['class' =>  $ticketType->id <> 3 ? 'form-control seat-ticket-price takePlace' : 'form-control seat-ticket-price', 'min' => '0','max' => $seatType->pivot->freeSeats,
                                'data-seattype' => $seatType->id,
                                'data-max' => $seatType->pivot->freeSeats,
                                'data-type' => $seatType->id,
                                'data-ticket' => $ticketType->id,
                                'data-did' => $reservation->getSeatsQuantity($seatType, $ticketType),
                               
                            ]) !!}
                        @endif
                    </div>
                </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>

<script>
//Disponibilidad de asientos.
    $('body').on('change', 'input.seat-ticket-price', function () {

        
        if (typeof $(this).data('max') != 'undefined') {
            $this = $(this);
            var max = $(this).data('max');
            var maxtot = $(this).data('max');
            var type = $(this).data('type');

            $typeInputs = $('table').find(".takePlace[data-max='" + max + "'][data-type='" + type + "']");


            var total = 0;
            $typeInputs.each(function (k, v) {
                if ($(this).val() == "")
                    var val = 0;
                else
                    var val = parseInt($(this).val());

                if ($(this).data('did')) {
                    max += parseInt($(this).data('did'));
                }
                total += parseInt(val);
            });

            //Basarme en esto para controlar los totales de pax, y limitarlos para las mesas.
            if (total > maxtot) {
                if (maxtot == 0) {
                    +
                        swal({
                            title: "No seats available",
                            confirmButtonColor: "#d9534f",
                            closeOnConfirm: true,
                            animation: "slide-from-top"
                        });
                } else {
                    swal({
                        title: "There are only " + maxtot + " seats availables",
                        confirmButtonColor: "#d9534f",
                        closeOnConfirm: true,
                        animation: "slide-from-top"
                    });
                }
                $this.val('0');
            }
        }
    });


</script>
