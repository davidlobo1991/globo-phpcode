<a href="{{ route('products.edit', $id) }}" class="btn btn-xs btn-primary"><i
            class="glyphicon glyphicon-edit"></i> {{ trans('common.edit') }}</a>

{!! Form::open(['route' => ['products.destroy', $id], 'method' => 'DELETE', 'class' => 'inline']) !!}
<button class="btn btn-xs btn-danger" id="remove"><i
            class="glyphicon glyphicon-remove"></i> {{ trans('common.delete') }}</button>
{!! Form::close() !!}

<a href="{{ route('products.prices', $id) }}" class="btn btn-xs btn-warning"><i
        class="glyphicon glyphicon-euro"></i> {{ trans('common.prices') }}</a>
