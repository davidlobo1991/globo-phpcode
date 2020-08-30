@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.buses'))

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <!-- Default box -->
                <div class="box">
                    <div class="box-header with-border">
                        <a href="{{ route('buses.create') }}" class="btn btn-primary">{{ trans('common.create') }}</a>
                    </div>
                    <div class="box-body">
                        @include('flash::message')

                        <table id="list-table" class="table table-striped table-hover table-condensed table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('common.product') }}</th>
                                <th>{{ trans('common.date') }}</th>
                                <th>{{ trans('common.route') }}</th>
                                <th>{{ trans('common.transporter') }}</th>
                                <th>{{ trans('common.capacity') }}</th>
                                <th style="width: 20%">{{ trans('common.actions') }}</th>
                            </tr>
                            </thead>
                        </table>
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
            {data: 'product'},
            {data: 'datetime'},
            {data: 'route'},
            {data: 'transporter'},
            {data: 'capacity'},
            {data: 'action', orderable: false, searchable: false}
          ],
          ajax: '{{ route('buses.data', request()->all()) }}'
        })
      })
    </script>
@endpush
