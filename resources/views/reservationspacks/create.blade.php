@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.reservations'))

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

                    {!! Form::open(['route' => 'reservationspacks.store', 'id' => 'mainForm','v-on:submit.prevent' => 'validateBeforeSubmit']) !!}
                    <div class="box-body">

                        @include('errors.errors')

                        @include('reservationspacks.form')

                    </div>

                    <div class="box-footer with-border">
                        <footer>
                            <input type="button" class="btn btn-success" id="save" value="{{ trans('common.save') }}" />
                            <a class="btn btn-danger" href="{{ route('reservations.index') }}">{{ trans('common.return') }}</a>
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
        const validateForm = function ()
        {
            if (! greaterThanAvailableSeats()) {
                $("form#mainForm").submit();
            }
        }

        function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
        }

        const greaterThanAvailableSeats = function ()
        {
            //Meter a cada select una class común.
            //Coger Quantity y comparar quantity < data-max del find("option:selected") cada select de dicha clase.
            let result = false;
            let name = $("input#name").val();
            let email = $("input#email").val();
            let phone = $("input#phone").val();
            let identification_number = $("input#identification_number").val();

            if (name.trim() === "") {        
            swal({
                    title: "Please enter a name",
                    confirmButtonColor: "#d9534f",
                    closeOnConfirm: true,
                    animation: "slide-from-top"
                });
                result = false;
                exit;
                
            }

            if (phone.trim() === "") {        
            swal({
                    title: "Please enter a phone",
                    confirmButtonColor: "#d9534f",
                    closeOnConfirm: true,
                    animation: "slide-from-top"
                });
                result = false;
                exit;
                
            }

            if (validateEmail(email)) {
                
            } else {
                swal({
                    title: "Please enter a emails",
                    confirmButtonColor: "#d9534f",
                    closeOnConfirm: true,
                    animation: "slide-from-top"
                });
                result = false;
                exit;
            }


            let quantity = parseInt($("input#quantity").val());
            $("select.select-pass").each(function()
            {
                let option = $(this).find("option:selected")
                //valida que esté vacio passes
                if (! option.length) {
                    result = true;
                }
                 //valida que esté la disponobilidad de passes
                if (parseInt(option.data("max")) < quantity) {
                    result = true;
                }
                 //valida que esté vacio el option de disponibilidad passes
                 if (option.data("max") === "") {
                    result = true;
                }
                
            });
            
            if (result) {
                swal({
                    title: "No seats available",
                    confirmButtonColor: "#d9534f",
                    closeOnConfirm: true,
                    animation: "slide-from-top"
                });
            }

            return result;
        }

        $('body').on('change', 'input#quantity', function() {

               
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


        $(function () {
            $("input#save").on("click", validateForm);

            $('#list-table').DataTable({
                processing: true,
                serverSide: true,
                columns: [
                    {data: 'name'},
                    {data: 'email'},
                    {data: 'action', orderable: false, searchable: false}
                ],
                ajax: '{{ route('reservations.data', request()->all()) }}'
            });
        });

    </script>
@endpush