@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.roles'))

@section('main-content')
    <div class="container-fluid spark-screen" id="app">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <!-- Default box -->
                <div class="box" id="userCreate">
                    <div class="box-header with-border">
                        <p class="lead">{{ trans('common.create') }} @yield('contentheader_title')</p>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                    title="Collapse">
                                <i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip"
                                    title="Remove">
                                <i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    {!! Form::open(['route' => 'roles.store', 'id' => 'mainForm','v-on:submit.prevent' => 'validateBeforeSubmit']) !!}
                    <div class="box-body">

                        @include('errors.errors')

                        @include('roles.form')

                    </div>

                    <div class="box-footer with-border">
                        <footer>
                            <button class="btn btn-success">{{ trans('common.save') }}</button>
                            <a class="btn btn-danger" href="{{ route('users.index') }}">{{ trans('common.return') }}</a>
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
                {data: 'role'},
                {data: 'email'},
                {data: 'action', orderable: false, searchable: false}
            ],
            ajax: '{{ route('users.data', request()->all()) }}'
        });
    });
</script>
@endpush