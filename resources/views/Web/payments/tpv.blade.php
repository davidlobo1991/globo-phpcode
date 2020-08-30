@extends('Web.payments.layouts.default')

@section('content')
    <div class="l-wrapper">
            @if(!empty($reservationsCreated['reservations_pirates']))
            @foreach($reservationsCreated['reservations_pirates'] as $item)
                {!! Form::hidden('reservation_pirates_id[]', $item->id, ['id'=>'reservation_pirates_id']) !!}
            @endforeach
            @endif

            @if(!empty($reservationsCreated['reservations_globo']))
            @foreach($reservationsCreated['reservations_globo'] as $item)
                {!! Form::hidden('reservation_globo_id[]', $item->id, ['id'=>'reservation_globo_id']) !!}
            @endforeach
            @endif


        <div class="l-grid__item ">
            <h3 class="c-title c-title--grey">YOUR ORDER</h3>
            <div class="c-cart-resume">

                <div class="c-cart-resume__item"><span> <i>Name:</i> {{ $cart->getFormAttribute('name') }} {{ $cart->getFormAttribute('last_name') }}</span></div>

                <div class="c-cart-resume__item"><span> <i>Email: </i>{{ $cart->getFormAttribute('email') }}</span></div>
                <div class="c-cart-resume__item"><span> <i> Payment method: </i>{{ $cart->getFormAttribute('payment_option') }}</span></div>


                <hr>
                @foreach($cart->getTPV() as $i => $item)
                @if($item->getSku() <> 'TAX-PAYPAL' or $item->getSku() == 'TAX-BOOKINGFEE')
                <div class="c-cart-resume__item"><span>{{ $item->name }} </span><span>{{ number_format($item->price,2) }} €</span></div>
                @endif
                @endforeach
                <hr>
                <div class="c-cart-resume__item"><span> <i>Total: </i>{{$cart->getSubtotal()}} € </span></div>



            </div>
        </div>
        <div class="l-grid__item u-1/1@m u-mrv-s">
            {!! $form !!}

        </div>

        <div class="l-grid__item u-1/1@m u-mrv-s">
          {{--//TODO return y borrar la session--}}
            <a href="//magalufessential.com" class="c-cart-resume__btn c-btn c-btn--full" >Return home</a>
        </div>


    </div>

@endsection

