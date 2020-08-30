@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.global'))

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <!-- Default box -->
                <div class="box">
                    <div class="box-header with-border">
                          <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                    title="Collapse">
                                <i class="fa fa-minus"></i></button>
                            
                        </div>
                    </div>
                    <div class="box-body">
                        @include('flash::message')

                        <table id="list-table" class="table table-striped table-hover table-condensed table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('common.booking_fee') }} €</th>
                                <th>{{ trans('common.paypal') }} % </th>
                                
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
               
                {data: 'booking_fee'},
                {data: 'paypal'},
                
                {data: 'action', orderable: false, searchable: false}
            ],
          ajax: '{{ route('global.data', request()->all()) }}'
        });
    });
</script>
@endpush