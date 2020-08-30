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

                        @if(!$reservation->reservationTickets->isEmpty())
                            @include('Email.pirates.products.detailTickets')
                        @endif
                        {{-- @if(!$reservation->ReservationTransport->isEmpty())
                            @include('Email.pirates.products.detailTransport')
                        @endif --}}
                        @if($reservation->promocodes)
                            @include('Email.pirates.products.detailPromocode')
                        @endif
                        @include('Email.pirates.products.detailPrice')


                        {{--modulos adicionales de la reserva --}}

                </div>
    </body>
    </html>


