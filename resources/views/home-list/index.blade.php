@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.home'))

@section('main-content')
@if(Auth::user()->role->role_permission('view-home-list'))
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
                        @include('home-list.search')
                        @if(!isset($request->viewType))
                            @include('home-list.availability')
                        @elseif($request->viewType == 'availability')
                            @include('home-list.availability')
                        @else
                            @include('home-list.sales')
                        @endif
                    </div>
                <!-- Default Content -->


                </div>
                <div class="text-center">
              
                {!! $passes->appends([
                    'viewType' => $viewType,
                    'products' => $request->products,
                    'dateStart' => $request->dateStart,
                    'dateEnd' => $request->dateEnd,
                ])->render() !!}

                </div>
             </div>
        </div>
    </div>
@endif



@endsection
