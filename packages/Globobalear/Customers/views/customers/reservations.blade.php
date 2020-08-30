@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.customers').': ('. count($customer->reservations).' ' .trans('menu.reservations').') by '. $customer->name )

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <!-- Default box -->
                <div class="box">
                    <div class="box-header with-border">
                        <a href="{{ route('customers.create') }}" class="btn btn-primary">{{ trans('common.create') }}</a>

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
                                <th>{{ trans('common.reservation_number') }}</th>
                                <th>{{ trans('common.product') }}</th>
                                <th>{{ trans('common.name') }}</th>
                                <th>{{ trans('common.email') }}</th>
                                <th>{{ trans('common.channel') }}</th>

                                <th>{{ trans('common.date') }}</th>
                                <th>ADU</th>
                                <th>CHD</th>
                                <th>INF</th>
                                <th>TOT</th>
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
                {data: 'reservation_number'},
                {data: 'name_reservation'},
                {data: 'name'},
                {data: 'email'},
                {data: 'channel'},

                {data: 'created_at'},
                {data: 'ADU'},
                {data: 'CHD'},
                {data: 'INF'},
                {data: 'TOT'},
                {data: 'action', orderable: false, searchable: false}
            ],
            ajax: '{{ route('customers.datareservations', $customer->id, request()->all()) }}'
        });
    });
</script>
@endpush
