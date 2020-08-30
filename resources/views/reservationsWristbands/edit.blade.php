@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.reservations'). ' - ' .$passSeller->channels->first()->name)

@section('main-content')
    <div class="container-fluid spark-screen" id="app">
        <div class="row">
            <div class="col-md-12">

                <div class="box" id="showCreate">
                    <div class="box-header with-border">
                        <p class="lead">{{ trans('common.edit') }} @yield('contentheader_title')</p>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                    title="Collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip"
                                    title="Remove">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>

                    {!! Form::model($reservation, ['route' => ['reservations-wristbands.update', $reservation->id], 'method' => 'PUT', 'id' => 'mainForm','v-on:submit.prevent' => 'validateBeforeSubmit']) !!}
                        <div class="box-body">
                            @include('errors.errors')
                            @include('reservationsWristbands.form')
                        </div>

                        <div class="box-footer with-border">
                            <footer>
                                <button class="btn btn-success">{{ trans('common.save') }}</button>
                                <a class="btn btn-danger" href="{{ route('reservations.index') }}">{{ trans('common.return') }}</a>
                            </footer>
                        </div>
                    {!! Form::close() !!}

                </div>
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
    </script>
@endpush