

<a href="{{route($reservation_type_id.'.show', $id) }}"  class="btn btn-xs btn-light" title='Reservation details'><i class="fa fa-info-circle" aria-hidden="true"></i> </a>
<a  class="cancel-booking btn btn-xs btn-light" data-id="{{ $id }}" title='Reservation Cancel'><i class="fa fa-ban" aria-hidden="true"></i> </a>
<a href="{{ route('payments.getPayments', $id) }}" class="btn btn-xs btn-light" title='Reservation Payment'><i class="fa fa-money" aria-hidden="true"></i></a>

<a href="{{ route($reservation_type_id.'.edit', $id) }}" class="btn btn-xs btn-primary" title='Reservation Edit'><i class="glyphicon glyphicon-edit"></i> </a>
{!! Form::open(['route' => ['reservations.destroyUnfinished', $id], 'method' => 'DELETE', 'class' => 'inline']) !!}
<button class="btn btn-xs btn-danger" id="remove"><i class="glyphicon glyphicon-remove" title='Reservation Delete'></i> </button>
{!! Form::close() !!}


