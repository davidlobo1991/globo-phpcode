<div class="col-md-12 table-responsive">
    <table class="table table-striped table-hover table-condensed table-responsive generatePassesTable">
        <thead>
        <tr>
            @foreach($seatTypes as $seatType)
                <th class="-inverse text-center">{{ $seatType->acronym }}</th>
                <th class="text-center" colspan="{{ $ticketTypes->count() }}">{{ trans('common.prices') }}</th>
            @endforeach
        </tr>
        <tr>
            @foreach($seatTypes as $seatType)
                <th class="text-center">{{ trans('common.seats') }}</th>
                @foreach($ticketTypes as $ticketType)
                    <th class="text-center">{{ $ticketType->acronym }}</th>
                @endforeach
            @endforeach
        </tr>
        </thead>
        <tbody>
        <tr>
            @foreach($seatTypes as $seatType)

                <td>{!! Form::number("seats[{$el}][{$seatType->id}]", $seatType->default_quantity, ['step' => '0.01', 'min' => 0, 'class' => 'form-control seats','readonly'=>false, 'required'=> 'required']) !!}</td>
                @foreach($ticketTypes as $ticketType)
                    <td>{!! Form::number("prices[{$el}][{$seatType->id}][{$ticketType->id}]", null, ['step' => '0.01', 'min' => 0, 'class' => 'form-control', 'required'=> 'required']) !!}</td>
                @endforeach
            @endforeach
        </tr>
        </tbody>
    </table>
</div>
