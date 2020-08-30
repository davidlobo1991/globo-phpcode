
@extends('layouts.app')

@section('content')
	<div class="container">
	<div class="content">
	<div class="error-page">
		<h2 class="headline text-green"> 403</h2>
		<div class="error-content">
			<h3><i class="fa fa-warning text-green"></i> Oops! Acceso denegado.</h3>
			<p>No tiene permiso para acceder a esta página o realizar esta acción.
			Mientras tanto, usted puede<a href="{{ asset('/') }}"> volver a la home</a>.</p>
		</div>
	</div>
	</div>
	</div>
@endsection