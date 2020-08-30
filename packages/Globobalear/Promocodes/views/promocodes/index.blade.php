@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.promocodes'))

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <!-- Default box -->
                <div class="box">
                    <div class="box-header with-border">
                        <a href="{{ route('promocodes.create') }}" class="btn btn-primary">{{ trans('common.create') }}</a>

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
                                <th>{{ trans('common.discount') }}</th>
                                <th>{{ trans('common.single_use') }}</th>
                                <th>{{ trans('common.valid_from') }}</th>
                                <th>{{ trans('common.valid_to') }}</th>
                                <th>{{ trans('common.for_from') }}</th>
                                <th>{{ trans('common.for_to') }}</th>
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
    $(function() {
        $('#list-table').DataTable({
            processing: true,
            serverSide: true,
            columns: [
                {data: 'code'},
                {data: 'discount'},
                {data: 'single_use'},
                {data: 'valid_from'},
                {data: 'valid_to'},
                {data: 'for_from'},
                {data: 'for_to'},
                {data: 'action', orderable: false, searchable: false}
            ],
            ajax: '{{ route('promocodes.data', request()->all()) }}'
        });
    });
</script>
@endpush