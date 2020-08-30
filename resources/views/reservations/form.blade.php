{!! Form::hidden('channel_id', $passSeller->channels->first()->id) !!}

{!! Form::hidden('reservation_type_id', \App\ReservationType::PRODUCTS) !!}


{{-- init customers--}}
    @include('customers::includes.reservations')
{{-- fin customers--}}

{{-- init channel--}}
    @include('partials.channels')
{{-- fin channel--}}

{{-- init productos--}}
    @if(isset($reservation))
        @include('products::includes.reservationsEdit')
    
    @else
        @include('products::includes.reservationsCreate')
    
    @endif
{{-- fin productos--}}

{{-- init promocodes--}}
@include('promocodes::includes.reservations')
{{-- fin promocodes--}}

{{-- init bookingFee--}}
@include('partials.bookingFee')
{{-- fin bookingFee--}}

@include('partials.comments')