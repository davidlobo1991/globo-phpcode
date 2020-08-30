<html>
<head>
<link rel="stylesheet" href="{{ asset('css/pdf.css') }}">
  
<head>
<body>
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">
            <!-- Default box -->
                 <div class="box-body">
                    <div class="container-fluid spark-screen" id="reservations">
                    <div class="row">
                        <div class="col-md-12"> 
                        <table id="list-table" class="table table-striped table-hover table-condensed table-responsive">

                        
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
                        @include('partials.reservationsDetail')
                        @include('partials.ticketsDetail')
                        @include('partials.transportDetail')
                        @include('partials.menuDetail')
                        @include('partials.promocodeDetail')
                        @include('partials.PriceDetail')
                        {{--modulos adicionales de la reserva --}}
                               </div>
                            </div>
                        </div>
                </div>

            </div>
        </div>
    </div>

