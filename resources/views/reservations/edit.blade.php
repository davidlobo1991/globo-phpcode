@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.reservations'))

@section('main-content')
    <div class="container-fluid spark-screen" id="reservations">
        <div class="row">
            <div class="col-md-12">
                
                <!-- Default box -->
                <div class="box" id="userEdit">
                    <div class="box-header with-border">
                        <p class="lead">{{ trans('common.edit') }} @yield('contentheader_title')</p>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                    title="Collapse">
                                <i class="fa fa-minus"></i></button>
                           
                        </div>
                    </div>
                   
                     <div class="container-fluid" >  
                    <div class="row">
                    <div class="col-md-12">
                    
                            <div class="pull-left">
                            <a class="btn btn-default" href="{{ route('reservations.pdf',$reservation->id ) }}" target="_blank"><i class="fa fa-file-pdf-o"></i> {{ trans('common.pdf') }}</a>
                            <a href="{{route('reservations.show',$reservation->id) }}"  class="btn btn-info" title='Reservation details'><i class="fa fa-info-circle" aria-hidden="true"></i> {{ trans('common.info') }} </a>
                            <a  class="btn btn-warning cancel-booking" data-id="{{ $reservation->id }}"><i class="glyphicon glyphicon-ban-circle"></i> {{ trans('common.cancel') }}</a>    
                            </div>
                            <div class="pull-right">
                            <a class="btn btn-danger" href="{{ route('reservations.index') }}">{{ trans('common.return') }}</a>
                            </div>
                    </div>
                    </div>
                     </div>

                    {!! Form::model($reservation, ['route' => ['reservations.update', $reservation->id], 'method' => 'PUT', 'id' => 'mainForm','v-on:submit.prevent' => 'validateBeforeSubmit', 'files' => true]) !!}

                    {!! Form::hidden('reservation_id', $reservation->id) !!}
                    <div class="box-body">

                        @include('errors.errors')

                        @include('reservations.form')

                    </div>

                    <div class="box-footer with-border">
                        <footer>
                            <button class="btn btn-success">{{ trans('common.save') }}</button>
                            <a class="btn btn-danger" href="{{ route('reservations.index') }}">{{ trans('common.return') }}</a>
                        </footer>
                    </div>
                    {!! Form::close() !!}

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

        </div>
    </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#list-table').DataTable({
            processing: true,
            serverSide: true,
            columns: [
                {data: 'name'},
                {data: 'email'},
                {data: 'action', orderable: false, searchable: false}
            ],
            ajax: '{{ route('reservations.data', request()->all()) }}'
        });
    });

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

                            swal("Canceled", "Reservations: " + id, "success"); 
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