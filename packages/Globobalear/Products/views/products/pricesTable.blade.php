<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-euro"></i> Ticket Prices</h4>
    </div>
    <div class="panel-body">
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
                <tr>
                    <td class="seatType">
                        {{ $seatType->title }}
                    </td>
                    @foreach($ticketTypes as $ticketType)
                        <td>
                            <div class="form-group input-group">
                                {!! Form::number("data[{$seatType->id}][ticketTypes][{$ticketType->id}]",
                                $productsPrices->where('seat_type_id', $seatType->id)->where('ticket_type_id', $ticketType->id)->pluck('price')->first() ?? '0.00',
                                    ['class' => 'form-control', 'min' => '0', 'step' => '0.01']) !!}
                                <span class="input-group-addon">€</span>
                            </div>
                        </td>
                    @endforeach
                </tr>
            @endforeach
            {!! Form::hidden("product_id", $product->id) !!}
            </tbody>
        </table>
    </div>
</div>
