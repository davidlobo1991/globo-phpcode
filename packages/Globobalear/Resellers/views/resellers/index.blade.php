@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.resellers'))

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <!-- Default box -->
                <div class="box">
                    <div class="box-header with-border">
                        <a href="{{ route('resellers.create') }}" class="btn btn-primary">{{ trans('common.create') }}</a>

                        
                    </div>
                    <div class="box-body">
                        @include('flash::message')

                        <table id="list-table" class="table table-striped table-hover table-condensed table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('common.name') }}</th>
                                <th>{{ trans('common.company') }}</th>
                                <th>{{ trans('common.email') }}</th>
                                <th>{{ trans('common.phone') }}</th>
                                <th>{{ trans('common.type') }}</th>
                                <th>{{ trans('common.agent') }}</th>
                                <th>{{ trans('common.area') }}</th>
                                <th>{{ trans('common.is_enable') }}</th>
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
                {data: 'company'},
                {data: 'email'},
                {data: 'phone'},
                {data: 'resellertype'},
                {data: 'agenttype'},
                {data: 'acronym'},
                {data: "is_enable", "render": function (data, type, row) {
                return (data == 1) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
                }
              },
                {data: 'action', orderable: false, searchable: false}
            ],
            ajax: '{{ route('resellers.data', request()->all()) }}'
        });
    });
</script>
@endpush