@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.reservations'))

@section('main-content')
    <div class="container-fluid spark-screen" id="app">
        <div class="row">
            <div class="col-md-12">

            <!-- Default box -->
                <div class="box">
                    <div class="box-header with-border">
                    <p class="lead">{{ trans('common.show') }} @yield('contentheader_title')</p>
                    <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                    title="Collapse">
                                <i class="fa fa-minus"></i></button>
                    </div>
                </div>

                 <div class="box-body">

                   
                    <div class="container-fluid spark-screen">
                    <div class="row">
                     <div class="col-md-12"> 
                            <div class="pull-left">
                            <a class="btn btn-default" href="{{ route('reservationspacks.pdf',$reservation->id ) }}" target="_blank"><i class="fa fa-file-pdf-o"></i> {{ trans('common.pdf') }}</a>
                            <a  class="btn btn-warning cancel-booking" data-id="{{ $reservation->id }}"><i class="glyphicon glyphicon-ban-circle"></i> {{ trans('common.cancel') }}</a>    
                            <a href="{{ route('reservationspacks.edit',  $reservation->id) }}" class="btn btn-primary"><i class="glyphicon glyphicon-edit"></i> {{ trans('common.edit') }}</a>
                            <a href="{{ route('payments.getPayments', $reservation->id) }}" class="btn btn-success" title='Reservation Payment'><i class="fa fa-money" aria-hidden="true"></i></a>  
                            {!! Form::open(array('route' => 'reservations.resendemail', 'id'=> 'resendemail','method' => 'post', 'style' => 'display:inline')) !!}
                            <input name="id_reservation" class="hidden" value="{{ $reservation->id }}" type="text">
                            <input class="btn btn-info" value="Resend Email" type="submit">
                            {!! Form::close() !!}
                            </div>
                            
                            <div class="pull-right">
                            <a class="btn btn-danger" href="{{ route('reservations.index') }}">{{ trans('common.return') }}</a>
                            </div>
                     </div>
                        <div class="col-md-12"> 
                        <table id="list-table" class="table table-striped table-hover table-condensed table-responsive">
                         <thead>
                         <tr>
                         @if($reservation->created_at)
                            <td class='text-success'>
                                <i class="fa fa-pencil"></i> Created
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
                                <i class="fa fa-pencil"></i> Canceled
                                @if($reservation->canceled_by_user)
                                    by <b>{{ $reservation->canceled_by_user->name }}</b>
                                @endif
                                at <b>{{ $reservation->canceled_date->format('d-m-Y H:i') }}</b>
                                <p > <i class="fa fa-commenting" aria-hidden="true"></i> {{ $reservation->canceled_reason }} </p>
                            </td>
                        @endif
                        </tr>

                        </thead>
                        </table>
                        {{--modulos adicionales de la reserva --}}
                        @include('partials.reservationsDetail')
                        @include('reservationspacks.packsDetail')
                        @include('partials.promocodeDetail')
                        @include('partials.comment')
                        @include('reservationspacks.PriceDetail')
                        @include('partials.payments')
                        {{--modulos adicionales de la reserva --}}

                        


                               
                            </div>
                        </div>
                </div>

            </div>
        </div>
    </div>
@endsection


@push('scripts')
<script>
  //Mensaje de Cancel Reservation
$(function () {
    $(document).on('click', ".cancel-booking", function (e) {
  
        e.preventDefault();
        var id = $(this).data('id');
        var reservationsUrl = "/reservations";

        swal({
            title: "CANCEL BOOKING",
            text: "Type <b>CANCEL</b> to cancel this booking",
            type: "input",
            html: true,
            cancelButtonText: 'Close',
            confirmButtonColor: "#ff1a1a",
            showCancelButton: true,
            closeOnConfirm: false,
            closeOnCancel: true,
            //animation: "slide-from-top",
            inputPlaceholder: "CANCEL"
        }, function (inputValue) {
            if (inputValue == "CANCEL") {
                swal({
                    title: "REASONS",
                    text: "Explain the reasons for the cancellation",
                    type: "input",
                    html: true,
                    cancelButtonText: 'Close',
                    confirmButtonColor: "#ff1a1a",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    inputPlaceholder: "Reasons",
                    showLoaderOnConfirm: true
                }, function (val) {
                    if (val.length > 4) {
                        $.post(
                            '/reservations/cancel', {id: id, reason: val}
                        ).done(function () {

                            swal("Nice!", "Canceled: " + id, "success"); 
                           //return false;
                           window.location.replace(reservationsUrl);

                        });
                    } else {
                        swal.showInputError("You need to write something!");
                        return false;
                    }
                });
                return false;
            } else {
                swal.showInputError("You need to write CANCEL!");
                return false;
            }
        });
    });
});

</script>
@endpush