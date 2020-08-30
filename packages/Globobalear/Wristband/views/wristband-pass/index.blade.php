@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.wristband-passes'))

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <!-- Default box -->
                <div class="box">
                    <div class="box-header with-border">
                        <a href="{{ route('wristband-pass.create') }}" class="btn btn-primary">{{ trans('common.create') }}</a>

                        
                    </div>
                    <div class="box-body">
                        @include('flash::message')

                        <table id="list-table" class="table table-striped table-hover table-condensed table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('common.title') }}</th>
                                <th>{{ trans('menu.wristband') }}</th>
                                <th>{{ trans('common.date_start') }}</th>
                                <th>{{ trans('common.date_end') }}</th>
                                <th>{{ trans('common.price') }}</th>
                                <th>{{ trans('common.quantity') }}</th>
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
                {data: 'wristband_id'},
                {data: 'date_start'},
                {data: 'date_end'},
                {data: 'price'},
                {data: 'quantity'},
                {data: 'action', orderable: false, searchable: false}
            ],
            ajax: '{{ route('wristband-pass.data', request()->all()) }}'
        });
    });
</script>
@endpush