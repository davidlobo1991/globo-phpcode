<!-- REQUIRED JS SCRIPTS -->

<!-- JQuery and bootstrap are required by Laravel 5.3 in resources/assets/js/bootstrap.js-->
<!-- Laravel App -->
<script src="{{ url (mix('/js/app.js')) }}" type="text/javascript"></script>
{!! Html::script("plugins/ckeditor/ckeditor.js") !!}
{!! Html::script("plugins/sweetalert/dist/sweetalert.min.js") !!}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
{!! Html::script("plugins/numeric/jquery.numeric.js") !!}
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="{{ asset('plugins/pace/pace.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/datepicker/bootstrap-datepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/timepicker/bootstrap-timepicker.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/select2/select2.full.js') }}" type="text/javascript"></script>


{!! Toastr::render() !!}

@stack('scripts')

<!-- Optionally, you can add Slimscroll and FastClick plugins.
      Both of these plugins are recommended to enhance the
      user experience. Slimscroll is required when using the
      fixed layout. -->