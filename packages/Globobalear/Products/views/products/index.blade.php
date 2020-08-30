@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.products'))

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <!-- Default box -->
                <div class="box">
                    <div class="box-header with-border">
                        <a href="{{ route('products.create') }}" class="btn btn-primary">{{ trans('common.create') }}</a>

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
                                <th>{{ trans('common.name') }}</th>
                                <th>{{ trans('common.acronym') }}</th>
                                <th>{{ trans('common.category') }}</th>
                                <th>{{ trans('common.commission') }}%</th>
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

                {data: 'name'},
                {data: 'acronym'},
                {name: 'categories.name', data: 'category'},
                {name: 'products.commission', data: 'commis'},
                {name: 'products.sort', data: 'orden'},
                {data: 'action', orderable: false, searchable: false}
            ],
          ajax: '{{ route('products.data', request()->all()) }}'
        });
    });
</script>
@endpush
