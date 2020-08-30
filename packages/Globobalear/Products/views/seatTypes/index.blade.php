@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.seatTypes'))

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <!-- Default box -->
                <div class="box">
                    <div class="box-header with-border">
                        <a href="{{ route('seatTypes.create') }}"
                           class="btn btn-primary">{{ trans('common.create') }}</a>

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
                                <th>{{ trans('common.title') }}</th>
                                <th>{{ trans('common.quantity') }}</th>
                                <th>{{ trans('common.acronym') }}</th>
                                <th>{{ trans('common.is_enable') }}</th>
                                <th>{{ trans('common.orden') }}</th>
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
    $(function() {
        $('#list-table').DataTable({
            processing: true,
            serverSide: true,
            columns: [
              {data: 'title'},
              {data: 'default_quantity'},
              {data: 'acronym'},
              {data: "is_enable", "render": function (data, type, row) {
                return (data == 'Yes') ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
                }
              },
              {data: 'sort'},
              {data: 'action', orderable: false, searchable: false}
            ],
          ajax: '{{ route('seatTypes.data', request()->all()) }}'
        });
    });
</script>
@endpush