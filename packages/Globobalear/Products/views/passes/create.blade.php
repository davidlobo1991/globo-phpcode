@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.passes'))

@section('main-content')
    <div class="container-fluid spark-screen" id="app">
        <div class="row">
            <div class="col-md-12">

                <!-- Default box -->
                <div class="box" id="showCreate">
                    <div class="box-header with-border">
                        <p class="lead">{{ trans('common.create') }} @yield('contentheader_title')</p>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                    title="Collapse">
                                <i class="fa fa-minus"></i></button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip"
                                    title="Remove">
                                <i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    {!! Form::open(['route' => 'passes.store', 'id' => 'mainForm','v-on:submit.prevent' => 'validateBeforeSubmit']) !!}
                    <div class="box-body">

                        @include('errors.errors')

                        <div class="col-md-12">
                            <button class="btn btn-primary" id="addLine">Add Line</button>

                            <hr>
                        </div>

                        <div class="col-md-12" id="lines">

                        </div>

                    </div>

                    <div class="box-footer with-border">
                        <footer>
                            <button class="btn btn-success">{{ trans('common.save') }}</button>
                            <a class="btn btn-danger"
                               href="{{ route('passes.index') }}">{{ trans('common.return') }}</a>
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
      let qty = 1

      $(document).ready(function () {
        $('#addLine').trigger('click')
      })

      $('#addLine').click(function (e) {
        e.preventDefault()

        $.post('/generateList', {element: qty})
          .done(function (data) {
            $('#lines').append(data)
          })

        qty++
      })

      $('body').on('click', '.deleteLine', function (e) {
        e.preventDefault()
        $(this).parent().parent().parent().remove()
      })

      $('body').on('change', '.showSelector', function (e) {
        e.preventDefault()
        let $selector = $(this)

        $.post('/tableProduct', {el: $(this).data('el'), product: $(this).val()})
          .done(function (data) {
            $selector.parent().parent().parent().find('.addTable').html(data)
          })
      })



    $('body').on('change', '.seats', function() {

               
                $(this).numeric({
                  negative: false
                }, function () {
                    this.value = 1;
                    this.focus();
                    $(this).attr('style', 'border:5px solid red');
                    $(this).css("background-color", "#fef9cf");
                 
                });

               if($(this).val() == ""){
                    $(this).val(1);     
                }
                if($(this).val() <= "0"){
                    $(this).val(1);     
                }
   
               
               
            
             });
    </script>
@endpush
