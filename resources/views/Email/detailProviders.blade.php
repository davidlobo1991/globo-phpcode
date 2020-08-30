<table width="900" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" bgcolor="#4ee2c0">&emsp;</td>
    </tr>
    <tr>
        <td align="center" bgcolor="#4ee2c0"><img style="max-width: 118px;" src="{{ url('/img/mallorcaessentials-logo.png') }}"/></td>
    </tr>
    <tr>
        <td align="center" bgcolor="#4ee2c0">&emsp;</td>
    </tr>
    <tr>
        <td align="center" bgcolor="#4ee2c0">
            <h1 style="color:#FFF">{{$reservation->name_reservation or '-'}} </h1>        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td align="center" bgcolor="#ff29c5"><h3>&emsp;RESERVATION NUMBER: {{$reservation->reservation_number or '-'}}</h3></td>
    </tr>

    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td bgcolor="#4ee2c0"><h3>&emsp;CUSTOMER DETAILS INFORMATION:</h3></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>
            <table width="900" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td><b>Name:</b> {{$reservation->name or '-'}} </td>
                    <td ><b>Phone:</b> {{$reservation->phone or '-'}}</td>

                </tr>
                <tr>
                    <td><b>Date: </b> {{$reservation->created_at->format('d-m-Y H:i') }}</td>
                    <td><b>Personal identification number:</b> {{$reservation->identification_number or ''}}</td>
                </tr>

            </table>       
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td bgcolor="#CCCCCC"><h3>&emsp;RESERVATION  INFORMATION:</h3></td>
    </tr>

    <tr>
        <td >&emsp;</td>
    </tr>
    <tr>
        <td>
            @if(!$reservation->reservationTickets->isEmpty())
                @include('Email.products.detailTickets')
                @include('Email.products.detailPromocode')
                @include('Email.products.detailPrice')
            @endif
            @if ($reservation->reservationPacks->count() || $reservation->ReservationPacksPirates->count())
                @include('Email.packs.detailPack')
                @include('Email.packs.detailTransport')
                @include('Email.packs.detailPromocode')
                @include('Email.packs.detailPrice')
            @endif
            @if ($reservation->reservationWristbandPasses->count())
                @include('Email.wristbands.detailWristband')
                @include('Email.wristbands.detailTransport')
                @include('Email.wristbands.detailPromocode')
                @include('Email.wristbands.detailPrice')
            @endif
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td bgcolor="#CCCCCC"><h3>&emsp;COMMENTS:</h3></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>{!! $reservation->comments or '-'!!}</td>
    </tr>
    <tr>
        <td align="center"><p><b>Please print off this confirmation and present at the box office in exchange for your tickets. </b></p></td>
    </tr>
</table>

<i class="clearfix"></i>


