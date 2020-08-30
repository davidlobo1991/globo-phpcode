@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.passes'))

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <!-- Default box -->
                <div class="box">
                    <div class="box-header with-border">
                        <a href="{{ route('passes.create') }}"
                           class="btn btn-primary">{{ trans('common.generate') }}</a>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                    title="Collapse">
                                <i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip"
                                    title="Remove">
                                <i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        @include('flash::message')

                        <table id="list-table" class="table table-striped table-hover table-condensed table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('menu.passes') }}</th>
                                <th>{{ trans('common.date') }}</th>
                                <th>{{ trans('common.product') }}</th>
                                <th>{{ trans('common.reservations_qty') }}</th>
                                <th>{{ trans('common.actions') }}</th>
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
        //: test column is the shows date, right now the format is Y/m/d, must be d/m/Y but the orderBy fails.
        $('#list-table').DataTable({
          processing: true,
          serverSide: true,
          columns: [
            {data: 'pass'},
            {data: {
                _:    "fecha",
                sort: "datetime"
            }, name:'datetime' },
            {data: 'name'},
            {data: 'total_reservations'},
            {data: 'action', orderable: false, searchable: false}
          ],
          ajax: '{{ route('passes.data', request()->all()) }}'
        })
      })
    </script>
@endpush
