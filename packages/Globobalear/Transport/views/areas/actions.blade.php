<a href="{{ route('areas.edit', $id) }}" class="btn btn-xs btn-primary"><i
            class="glyphicon glyphicon-edit"></i> {{ trans('common.edit') }}</a>

{!! Form::open(['route' => ['areas.destroy', $id], 'method' => 'DELETE', 'class' => 'inline']) !!}
<button class="btn btn-xs btn-danger" id="remove"><i class="glyphicon glyphicon-remove"></i> {{ trans('common.delete') }}</button>
{!! Form::close() !!}