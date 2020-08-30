@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.dishes'))

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <!-- Default box -->
                <div class="box">
                    <div class="box-header with-border">
                        <a href="{{ route('dishes.create') }}" class="btn btn-primary">{{ trans('common.create') }}</a>
                    </div>
                    <div class="box-body">
                        @include('flash::message')

                        <table id="list-table" class="table table-striped table-hover table-condensed table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('common.name') }}</th>
                                <th>{{ trans('common.type') }}</th>
                                <th>{{ trans('common.vegetarian') }}</th>
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
            {data: 'type'},
            {data: 'vegetarian'},
            {data: 'action', orderable: false, searchable: false}
          ],
          ajax: '{{ route('dishes.data', request()->all()) }}'
        })
      })
    </script>
@endpush