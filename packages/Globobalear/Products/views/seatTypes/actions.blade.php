<a href="{{ route('seatTypes.edit', $id) }}" class="btn btn-xs btn-primary"><i
            class="glyphicon glyphicon-edit"></i> {{ trans('common.edit') }}</a>

{!! Form::open(['route' => ['seatTypes.destroy', $id], 'method' => 'DELETE', 'class' => 'inline']) !!}
@if(!$is_enable)
    <button class="btn btn-xs btn-success"><i class="glyphicon glyphicon-check"></i> {{ trans('common.activate') }}
    </button>
@else
    <button class="btn btn-xs btn-danger" id="remove"><i
                class="glyphicon glyphicon-remove"></i> {{ trans('common.cancel') }}</button>
@endif
{!! Form::close() !!}
