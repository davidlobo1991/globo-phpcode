<table width="100%" style="border-spacing: 11px; border-collapse: separate;">
    <thead>
        <tr>
            <th>Seat Types</th>
            <th>Ticket Types</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($seatypes as $seatType)
            @php
            $packlineSeatType = $packlines->where('seat_type_id', $seatType->id);
            @endphp
            <tr>
                <td>
                    {!! Form::checkbox("products[{$el}][seatTypes][{$seatType->id}][id]", $seatType->id, !$packlineSeatType->isEmpty(), ['class' => 'iCheck js-on-disable', 'data-on-disable-target' => ".productCheck{$el}SeatType{$seatType->id}"]) !!} {{ $seatType->title }}
                </td>

                <td>
                    <table width="100%">
                        <tbody>
                            @foreach ($ticketTypes as $ticketType)
                                @php
                                $packlineSeatTypeTicketType = $packlineSeatType->where('ticket_type_id', $ticketType->id)->first();
                                @endphp
                                <tr>
                                    <td>
                                        {!! Form::checkbox("products[{$el}][seatTypes][{$seatType->id}][ticketTypes][{$ticketType->id}][id]", $ticketType->id, $packlineSeatTypeTicketType, ['class' => "iCheck productCheck{$el}SeatType{$seatType->id} js-on-disable js-product-ticket-type", 'disabled' => $packlineSeatType->isEmpty(), 'data-on-disable-target' => ".productCheck{$el}SeatType{$seatType->id}ticketType{$ticketType->id}"]) !!} {{ $ticketType->title }}
                                    </td>
                                    <td>
                                        {!! Form::number("products[{$el}][seatTypes][{$seatType->id}][ticketTypes][{$ticketType->id}][price]", $packlineSeatTypeTicketType ? $packlineSeatTypeTicketType->price : null, ['class' => "form-control productCheck{$el}SeatType{$seatType->id}ticketType{$ticketType->id}", 'disabled' => !$packlineSeatTypeTicketType, 'required' => true]) !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>

            </tr>
        @endforeach
    </tbody>

</table>
