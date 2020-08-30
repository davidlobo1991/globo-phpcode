@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.menus'))

@section('main-content')
    <div class="container-fluid spark-screen" id="app">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <!-- Default box -->
                <div class="box" id="showCreate">
                    <div class="box-header with-border">
                        <p class="lead">{{ trans('common.create') }} @yield('contentheader_title')</p>
                    </div>

                    {!! Form::open(['route' => 'menus.store', 'id' => 'mainForm','v-on:submit.prevent' => 'validateBeforeSubmit']) !!}
                    <div class="box-body">

                        @include('errors.errors')

                        @include('menus::menus.form')

                    </div>

                    <div class="box-footer with-border">
                        <footer>
                            <button class="btn btn-success">{{ trans('common.save') }}</button>
                            <a class="btn btn-danger" href="{{ route('menus.index') }}">{{ trans('common.return') }}</a>
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