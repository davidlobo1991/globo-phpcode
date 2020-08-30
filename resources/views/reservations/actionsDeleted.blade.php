
{!! Form::open(['route' => ['reservations.restore', $id], 'method' => 'POST', 'class' => 'inline']) !!}
<button class="btn btn-xs btn-danger" id="restore"><i
            class="glyphicon glyphicon-refresh"></i> {{ trans('common.restore') }}</button>
{!! Form::close() !!}