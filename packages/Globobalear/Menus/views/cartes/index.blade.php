@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.cartes'))

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <!-- Default box -->
                <div class="box">
                    <div class="box-header with-border">
                        <a href="{{ route('cartes.create') }}" class="btn btn-primary">{{ trans('common.create') }}</a>
                    </div>
                    <div class="box-body">
                        @include('flash::message')

                        <table id="list-table" class="table table-striped table-hover table-condensed table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('common.name') }}</th>
                                <th>{{ trans('common.product') }}</th>
                                <th>{{ trans('common.seat_type') }}</th>
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
        $('#list-table').DataTable({
          processing: true,
          serverSide: true,
          columns: [
            {data: 'name'},
            {data: 'product'},
            {data: 'seat_type'},
            {data: 'action', orderable: false, searchable: false}
          ],
          ajax: '{{ route('cartes.data', request()->all()) }}'
        })
      })
    </script>
@endpush
