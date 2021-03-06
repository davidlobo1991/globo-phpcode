<a href="{{ route('passes.edit', $id) }}" class="btn btn-xs btn-primary"><i
            class="glyphicon glyphicon-edit"></i> {{ trans('common.edit') }}</a>

{!! Form::open(['route' => ['passes.destroy', $id], 'method' => 'DELETE', 'class' => 'inline']) !!}
@if(!$on_sale)
    <button class="btn btn-xs btn-success"><i class="glyphicon glyphicon-check"></i> {{ trans('common.activate') }}
    </button>
@else
    <button class="btn btn-xs btn-danger" id="remove"><i
                class="glyphicon glyphicon-remove"></i> {{ trans('common.cancel') }}</button>
@endif
{!! Form::close() !!}