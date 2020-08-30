@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.cartes'))

@section('main-content')
    <div class="container-fluid spark-screen" id="cartes">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">

                <!-- Default box -->
                <div class="box" id="userEdit">
                    <div class="box-header with-border">
                        <p class="lead">{{ trans('common.edit') }} @yield('contentheader_title')</p>
                    </div>

                    {!! Form::model($carte, ['route' => ['cartes.update', $carte->id], 'method' => 'PUT', 'id' => 'mainForm','v-on:submit.prevent' => 'validateBeforeSubmit']) !!}


                    <div class="box-body">

                        @include('errors.errors')

                        @include('menus::cartes.form')

                    </div>

                    <div class="box-footer with-border">
                        <footer>
                            <button class="btn btn-success">{{ trans('common.save') }}</button>
                            <a class="btn btn-danger"
                               href="{{ route('cartes.index') }}">{{ trans('common.return') }}</a>
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