@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.roles'))

@section('main-content')
    <div class="container-fluid spark-screen" id="roles">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

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

                    {!! Form::model($roles, ['route' => ['roles.update', $roles->id], 'method' => 'PUT', 'id' => 'mainForm','v-on:submit.prevent' => 'validateBeforeSubmit']) !!}


                    <div class="box-body">

                        @include('errors.errors')
                         @include('roles.form')

                    </div>

                    <div class="box-footer with-border">
                        <footer>
                            <button class="btn btn-success">{{ trans('common.save') }}</button>
                            <a class="btn btn-danger" href="{{ route('roles.index') }}">{{ trans('common.return') }}</a>
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
                {data: 'guard_name'},
                {data: 'action', orderable: false, searchable: false}
            ],
            ajax: '{{ route('roles.data', request()->all()) }}'
        });

      $('input').iCheck({
      checkboxClass: 'icheckbox_square-green',
      radioClass: 'iradio_square-green',
      increaseArea: '20%' // optional
    });
    });
</script>
@endpush