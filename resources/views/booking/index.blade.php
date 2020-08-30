@extends('adminlte::layouts.app')

@section('contentheader_title', trans('common.booking'))

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
                <!-- Default Content -->
                     <div class="box-body">
                         @include('flash::message')
                         @include('booking.list')
                     </div>
                <!-- Default Content -->


                </div>
               
             </div>
        </div>
    </div>




@endsection
