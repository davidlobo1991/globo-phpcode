<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-sitemap"></i> Seats Availability</h4>
        {!! Form::hidden('pass_id', $pass->id) !!}
    </div>
    <div class="panel-body">
        @foreach($pass->seatTypes as $seatType)
            <div class="form-group col-md-3">
                <float-label>
                    {!! Form::number("data[{$seatType->id}][qty]", $seatType->pivot->seats_available,
                        ['class' => 'form-control', 'placeholder' => $seatType->title]) !!}
                </float-label>
            </div>
        @endforeach
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-euro"></i> Ticket Prices</h4>
    </div>
    <div class="panel-body">
        <table id="ticketTable" class="table table-responsive table-condensed text-center">
            <thead>
            <tr>
                <th class="empty col-md-2"></th>
                @foreach($pass->product->ticketTypes as $ticketType)
                    <th>{{ $ticketType->title }}</th>
                @endforeach
                <th>Web available</th>
            </tr>
            </thead>
            <tbody>
            @foreach($pass->seatTypes as $seatType)
                <tr>
                    <td class="seatType">
                        {{ $seatType->title }}
                    </td>
                    @foreach($pass->product->ticketTypes as $ticketType)
                        <td>
                            <div class="form-group input-group">
                                {!! Form::number("data[{$seatType->id}][ticketTypes][{$ticketType->id}]",
                                    $seatType->pivot->ticketTypes->where('id', $ticketType->id)->first()->pivot->price,
                                    ['class' => 'form-control', 'min' => '0', 'step' => '0.01']) !!}
                                <span class="input-group-addon">€</span>
                            </div>
                        </td>
                    @endforeach
                    <td>
                        <div class="form-group input-group">
                            {!! Form::checkbox("data[{$seatType->id}][web_available]", 1, $seatType->pivot->web_available, ['class' => 'iCheck']) !!}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

