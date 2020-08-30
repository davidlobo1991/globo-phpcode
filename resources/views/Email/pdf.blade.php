<html>
<head>
<link rel="stylesheet" href="{{ asset('css/pdf.css') }}">
</head>
<body>

                        <div class="container-fluid spark-screen">
                        <table border="0" cellspacing="0" cellpadding="0" >


                        <tr>
                            <td align="center" bgcolor="#4ee2c0"><img src="{{ env('APP_URL')}}/img/logo-email.png"/></td>
                        </tr>

                         <thead>
                         <tr>
                         @if($reservation->created_at)
                            <td class='text-success'>
                                Created
                                @if($reservation->created_by_user)
                                    by <b>{{ $reservation->created_by_user->name }}</b>
                                @endif
                                at <b>{{ $reservation->created_at->format('d-m-Y H:i') }}</b>
                            </td>
                        @endif
                        </tr>
                        <tr>
                         @if($reservation->canceled_date)
                            <td class='text-danger'>
                                 Canceled
                                @if($reservation->canceled_by_user)
                                    by <b>{{ $reservation->canceled_by_user->name }}</b>
                                @endif
                                at <b>{{ $reservation->canceled_date->format('d-m-Y H:i') }}</b>
                                <p > {{ $reservation->canceled_reason }} </p>
                            </td>
                        @endif
                        </tr>

                        </thead>
                        </table>

                        {{--modulos adicionales de la reserva --}}
                        @if(!$reservation->reservationTickets->isEmpty())
                        @include('Email.products.detailTickets')
                        @include('Email.products.detailTransport')
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


                        {{--modulos adicionales de la reserva --}}

                </div>
    </body>
    </html>


