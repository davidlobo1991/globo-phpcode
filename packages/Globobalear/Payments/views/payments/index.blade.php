@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.payments'))

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">

                <div class="col-md-12 ">

                <!-- Default box -->
                <div class="box">
                    <div class="box-body">
                    <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4><i class="fa fa-money"></i> {{trans('common.details')}} {{trans('menu.payments')}} </h4>
                            </div>

                            <div class="panel-body">

                                    {!! Form::open(['route' => 'payments.index', 'method' => 'POST']) !!}

                                            <div class="col-md-12 col-xs-12">
                                                <div class="text-center">
                                                 <div class="col-md-12">

                                                       <label class="custom-control custom-checkbox">
                                                        {!! Form::radio('viewType', 'method', $viewType == 'method') !!} Payment method
                                                        {!! Form::radio('viewType', 'reservations', $viewType == 'reservations') !!} Only Reservations

                                                        </label>

                                                    <hr>

                                                    </div>
                                                    <div class="col-md-12 col-xs-12">
                                                         <div class="form-group col-xs-12  col-md-4 text-left">
                                                        {{ Form::label('Product') }}
                                                        {!! Form::select('product',$products,null, ['id' => 'product', 'class' => 'form-control select']) !!}
                                                        </div>
                                                        <div class="form-group col-xs-12  col-md-4 text-left">
                                                        {{ Form::label('Pass') }}
                                                        {!! Form::select('pass',$passes,null, ['id' => 'pass', 'class' => 'form-control select']) !!}
                                                        </div>
                                                        <div class="form-group col-xs-12  col-md-4 text-left">
                                                        {{ Form::label('Pack') }}
                                                        {!! Form::select('pack',$packs,null, ['id' => 'pack', 'class' => 'form-control select']) !!}
                                                        </div>

                                                    </div>
                                                    <div class="col-md-12">
                                                    <div class="form-group col-xs-12  col-md-4 text-left">
                                                        {{ Form::label('Wristband') }}
                                                        {!! Form::select('wristband',$wristbandPass,null, ['id' => 'wristband', 'class' => 'form-control select']) !!}
                                                        </div>
                                                        <div class="form-group col-xs-12  col-md-4 text-left">
                                                        {{ Form::label('User') }}
                                                        {!! Form::select('user',$users,null, ['id' => 'user', 'class' => 'form-control select']) !!}
                                                        </div>
                                                        <div class="form-group col-xs-12  col-md-4 text-left">
                                                        {{ Form::label('Date') }}
                                                        {!! Form::text('date', null, ['id' => 'date','class' => 'form-control', 'readonly' => 'true']) !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                    {!! Form::submit('Search', ['class' => 'btn btn-info form-control']) !!}
                                                    </div>

                                                </div>
                                            </div>
                                    {!! Form::close() !!}

                                    <div class="col-md-12">
                                     <div class="text-center">
                                        {!! Form::open(['route' => 'paymentXML', 'method' => 'POST']) !!}
                                            <div class="form-group col-xs-12  col-md-6">
                                                {!! Form::label('download', 'Download XML') !!}
                                                {!! Form::hidden('date', isset($request->date) ? $request->date : null) !!}
                                                {!! Form::hidden('user', isset($request->user) ? $request->user : null) !!}
                                                {!! Form::hidden('product', isset($request->product) ? $request->product : null) !!}
                                                {!! Form::hidden('pass', isset($request->pass) ? $request->pass : null) !!}
                                                {!! Form::hidden('pack', isset($request->pack) ? $request->pack : null) !!}
                                                {!! Form::hidden('wristband', isset($request->wristband) ? $request->wristband : null) !!}
                                                {!! Form::button('<i class="fa fa-download"></i> Download', ['type' => 'submit','class' => 'form-control btn btn-danger']) !!}
                                            </div>
                                        {!! Form::close() !!}

                                         {!! Form::open(['route' => 'paymentEXCEL', 'method' => 'POST']) !!}
                                            <div class="form-group col-xs-12  col-md-6">
                                                {!! Form::label('download', 'Download EXCEL') !!}
                                                {!! Form::hidden('date', isset($request->date) ? $request->date : null) !!}
                                                {!! Form::hidden('user', isset($request->user) ? $request->user : null) !!}
                                                {!! Form::hidden('product', isset($request->product) ? $request->product : null) !!}
                                                {!! Form::hidden('pass', isset($request->pass) ? $request->pass : null) !!}
                                                {!! Form::hidden('pack', isset($request->pack) ? $request->pack : null) !!}
                                                {!! Form::hidden('wristband', isset($request->wristband) ? $request->wristband : null) !!}
                                                {!! Form::button('<i class="fa fa-file-excel-o"></i> Download', ['type' => 'submit', 'class' => 'form-control btn btn-success']) !!}
                                            </div>
                                        {!! Form::close() !!}


                                        </div>
                                     </div>


                                    @if ($payments->count())
                                        @if(!isset($request->viewType))
                                        @include('payments::payments.partialsPayment.methodglobal')
                                        @elseif($request->viewType == 'method')
                                        @include('payments::payments.partialsPayment.methodglobal')
                                        @else
                                        @include('payments::payments.partialsPayment.reservationglobal')
                                        @endif
                                    @endif


                        </div>
                    </div>

                </div>
            </div>

        </div>
        <div class="text-center">


        {!! $payments->appends([
                    'viewType' => $viewType,
                    'date' => $request->date,
                    'user' => $request->user,
                    'pass' => $request->pass,
                    'pack' => $request->pack,
                    'wristband' => $request->wristband,
                ])->render() !!}

        </div>
    </div>



@endsection

@push('scripts')
    <script type="text/javascript">
      $('select.select').select2({
        placeholder: 'Select a:'
      })

    $(function () {
    $('#date').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    format: "dd-mm-yyyy",
    todayHighlight: true,
    clearBtn: true,
    todayBtn: "linked",
    });

});


var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
    type:  'bar',
    data: {
        labels: [

            @foreach($payments as $paid)
               "{{ $paid->reservation_number }} - {{ $paid->customers_name }}",
            @endforeach
            ],
        datasets: [{
            label: 'Payments',
            data: [
                @foreach($payments as $paid)
                {{ $paid->total }},
                @endforeach
                ],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});

    </script>
@endpush


