@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.products'))

@section('main-content')
    <div class="container-fluid spark-screen" id="products">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <!-- Default box -->
                <div class="box" id="userEdit">
                    <div class="box-header with-border">
                        <p class="lead">{{ trans('common.edit') }} @yield('contentheader_title')</p>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                    title="Collapse">
                                <i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip"
                                    title="Remove">
                                <i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    {!! Form::model($product, ['route' => ['products.update', $product->id], 'method' => 'PUT', 'id' => 'mainForm','v-on:submit.prevent' => 'validateBeforeSubmit', 'files' => true]) !!}
                    <div class="box-body">

                        @include('errors.errors')

                        @include('products::products.form')

                    </div>

                    <div class="box-footer with-border">
                        <footer>
                            <button class="btn btn-success">{{ trans('common.save') }}</button>
                            <a class="btn btn-danger" href="{{ route('products.index') }}">{{ trans('common.return') }}</a>
                        </footer>
                    </div>
                    {!! Form::close() !!}

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

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
            {data: 'email'},
            {data: 'orden'},
            {data: 'action', orderable: false, searchable: false}
          ],
          ajax: '{{ route('products.data', request()->all()) }}'
        })
      })
    </script>
@endpush
