{!! Form::hidden('channel_id', $passSeller->channels->first()->id ??  null) !!}

{!! Form::hidden('pack_id', $passSeller->channels->first()->id ?? null) !!}

{!! Form::hidden('reservation_type_id', \App\ReservationType::PACKS) !!}

{{-- init customers--}}
    @include('customers::includes.reservations')
{{-- fin customers--}}


{{-- init channel--}}
    @if($passSeller->channels->first()->id <> 3)
        @include('resellers::includes.reservations')
    @else
        @include('partials.channels')
    @endif
{{-- fin channel--}}

{{-- init productos--}}
    @if(isset($reservation))
        @include('packs::includes.reservationsEdit')
    
    @else
        @include('packs::includes.reservationsCreate')
    
    @endif
{{-- fin productos--}}

{{-- init promocodes--}}
@include('promocodes::includes.reservations')
{{-- fin promocodes--}}

{{-- init bookingFee--}}
@include('partials.bookingFee')
{{-- fin bookingFee--}}

@include('partials.comments')