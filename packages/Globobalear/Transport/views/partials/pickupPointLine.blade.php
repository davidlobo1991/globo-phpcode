<div class="countPoints col-md-12">
    <hr>

    <div class="row">
        <div class="form-group col-md-5">
            {!! Form::label('Name') !!}
            {!! Form::text('name', $pickupPoint->name, ['class' => 'form-control', 'placeholder' => 'Name', 'disabled']) !!}
        </div>

        <div class="form-group col-md-5">
            {!! Form::label('City') !!}
            {!! Form::text('city_id', $pickupPoint->city->name, ['class' => 'form-control', 'placeholder' => 'City', 'disabled']) !!}
        </div>

        <div class="form-group col-md-2">
            <div class="btn btn-block btn-danger" style="margin-top: 25px;" id="deletePoint">
                <i class="fa fa-trash"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-4">
            {!! Form::label('Price') !!}
            @if(isset($pickupPoint->pivot))
                {!! Form::number("pickupPoints[{$pickupPoint->id}][price]", $pickupPoint->pivot->price, ['class' => 'form-control', 'placeholder' => 'Price', 'step' => '0.01']) !!}
            @else
                {!! Form::number("pickupPoints[{$pickupPoint->id}][price]", null, ['class' => 'form-control', 'placeholder' => 'Price', 'step' => '0.01']) !!}
            @endif
        </div>
        <div class="form-group col-md-4">
            {!! Form::label('Hour') !!}
            @if(isset($pickupPoint->pivot))
                {!! Form::time("pickupPoints[{$pickupPoint->id}][hour]", $pickupPoint->pivot->hour, ['class' => 'form-control', 'placeholder' => 'Hour']) !!}
            @else
                {!! Form::time("pickupPoints[{$pickupPoint->id}][hour]", null, ['class' => 'form-control', 'placeholder' => 'Hour']) !!}
            @endif
        </div>
        <div class="form-group col-md-4">
            {!! Form::label('Order') !!}
            @if(isset($order))
                {!! Form::text("pickupPoints[{$pickupPoint->id}][order]", $order, ['class' => 'form-control', 'placeholder' => 'Order']) !!}
            @else
                {!! Form::text("pickupPoints[{$pickupPoint->id}][order]", $pickupPoint->pivot->order, ['class' => 'form-control', 'placeholder' => 'Order']) !!}
            @endif
        </div>
    </div>
</div>
