@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.payments'))

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <!-- Default box -->
                <div class="box">
                    <div class="box-body">

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4><i class="fa fa-money"></i> {{trans('common.paid')}} </h4>
                            </div>
                            <div class="panel-body">
                                @include('payments::payments.detail')
                                <div class="box-header">
                                    <div class="pull-right">
                                        <button type="button" data-toggle="collapse" data-target="#payment{{$reservation->id}}" aria-expanded="false" aria-controls="collapseExample">
                                            <i class="fa fa-chevron-down" aria-hidden="true"></i> {{ trans('common.details')}}
                                        </button>
                                    </div>
                                </div>
                                <div class="collapse" id="payment{{$reservation->id}}">

                                    @if(!$reservation->reservationTickets->isEmpty())
                                        @include('payments::payments.ticket')
                                    @endif

                                    @if ($reservation->reservationPacks->count() || $reservation->ReservationPacksPirates->count())
                                        @include('payments::payments.pack')
                                    @endif

                                    @if ($reservation->reservationWristbandPasses->count())
                                        @include('payments::payments.wristband')
                                    @endif

                                    @include('payments::payments.transport')
                                    @include('payments::payments.promocode')
                                </div>

                                @if(!$reservation->reservationTickets->isEmpty())
                                    @include('payments::payments.paymentMethodTicket')
                                @endif

                                @if ($reservation->reservationPacks->count() || $reservation->ReservationPacksPirates->count())
                                    @include('payments::payments.paymentMethodPack')
                                @endif

                                @if ($reservation->reservationWristbandPasses->count())
                                    @include('payments::payments.paymentMethodWristband')
                                @endif



                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

        </div>
@endsection

