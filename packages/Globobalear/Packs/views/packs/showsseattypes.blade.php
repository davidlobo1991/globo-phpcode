@php
if ($ticketTypes->isEmpty()) {
    $ticketTypes = $allTicketTypesPirates;
}
@endphp

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
            $packlineSeatType = $packlinesPirates->where('seat_type_id', $seatType->id);
            @endphp
            <tr>
                <td>
                    {!! Form::checkbox("showspirates[{$elpirates}][seatTypes][{$seatType->id}][id]", $seatType->id, !$packlineSeatType->isEmpty(), ['class' => 'iCheck js-on-disable', 'data-on-disable-target' => ".showCheck{$elpirates}SeatType{$seatType->id}"]) !!} {{ $seatType->title }}
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
                                        {!! Form::checkbox("showspirates[{$elpirates}][seatTypes][{$seatType->id}][ticketTypes][{$ticketType->id}][id]", $ticketType->id, $packlineSeatTypeTicketType, ['class' => "iCheck showCheck{$elpirates}SeatType{$seatType->id} js-on-disable", 'disabled' => $packlineSeatType->isEmpty(), 'data-on-disable-target' => ".showCheck{$elpirates}SeatType{$seatType->id}ticketType{$ticketType->id}"]) !!} {{ $ticketType->title }}
                                    </td>
                                    <td>
                                        {!! Form::number("showspirates[{$elpirates}][seatTypes][{$seatType->id}][ticketTypes][{$ticketType->id}][price]", $packlineSeatTypeTicketType ? $packlineSeatTypeTicketType->price : null, ['class' => "form-control showCheck{$elpirates}SeatType{$seatType->id}ticketType{$ticketType->id}", 'disabled' => !$packlineSeatTypeTicketType, 'required' => true, 'step' => '0.01']) !!}
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
