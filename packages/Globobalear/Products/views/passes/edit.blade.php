@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.passes'))

@section('main-content')
    <div class="container-fluid spark-screen" id="passes">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">

                <!-- Default box -->
                <div class="box" id="userEdit">
                    <div class="box-header with-border">
                        <p class="lead">{{ trans('common.edit') }} @yield('contentheader_title')</p>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                    title="Collapse">
                                <i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip"
                                    title="Remove">
                                <i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    {!! Form::open(['route' => ['passes.update', $pass->id], 'method' => 'PUT', 'id' => 'mainForm','v-on:submit.prevent' => 'validateBeforeSubmit']) !!}


                    <div class="box-body">

                        @include('errors.errors')

                        @include('products::passes.form')

                    </div>

                    <div class="box-footer with-border">
                        <footer>
                            <button class="btn btn-success">{{ trans('common.save') }}</button>
                            <a class="btn btn-danger"
                               href="{{ route('passes.index') }}">{{ trans('common.return') }}</a>
                        </footer>
                    </div>
                    {!! Form::close() !!}

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

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
          ajax: '{{ route('passes.data', request()->all()) }}'
        })
      })
    </script>
@endpush
