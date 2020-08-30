 <a href="{{route('reservations.show', $id) }}"  class="btn btn-xs btn-info" title='Reservation details'>
<i class="glyphicon glyphicon-info-sign"></i> {{ trans('common.show') }}</a></a>

<a href="{{ route('reservations.edit', $id) }}" class="btn btn-xs btn-primary"><i
            class="glyphicon glyphicon-edit"></i> {{ trans('common.edit') }}</a>

{!! Form::open(['route' => ['reservations.destroy', $id], 'method' => 'DELETE', 'class' => 'inline']) !!}
<button class="btn btn-xs btn-danger" id="remove"><i
            class="glyphicon glyphicon-remove"></i> {{ trans('common.delete') }}</button>
{!! Form::close() !!}