    <div class="panel panel-default">
    <div class="panel-heading">
    <h4>  <i class="fa fa-ticket"></i>
                                   {{ trans('common.reservation') }} {{ trans('common.details') }}   </h4>
        </div>
    <div class="panel-body">
                   <table class="table table-hover table-responsive">
                            
                            <tbody>
                           
                            @if($reservation->name)
                            <td><label><b>{{ trans('common.name') }} </b></label>: {{$reservation->name or ''}} </td>
                            @endif
                            @if($reservation->phone)
                            <td><label><b>{{ trans('common.phone') }} </b></label>: {{$reservation->phone or ''}}</td>
                            @endif
                            @if($reservation->email)
                            <td><label><b>{{ trans('common.email') }} </b></label>: {{$reservation->email or ''}}</td>
                            @endif
                            @if($reservation->created_at)
                            <td><label><b>{{ trans('common.date') }}</b></label>: {{$reservation->created_at->format('d-m-Y H:i')}} </td>
                            @endif
                            </tbody>
                        </table>
 
                        <table class="table table-hover table-responsive">
                           
                            <tbody>
                            @if($reservation->reservation_number)
                            <td><label><b>{{ trans('common.reservation_number') }}</b></label>: {{$reservation->reservation_number or ''}}  </td>
                            @endif
                            @if($reservation->identification_number)
                            <td><label><b>{{ trans('common.reservation_numberTicket') }}</b></label>: {{$reservation->identification_number or ''}}  </td>
                            @endif
                            @if($reservation->pass)
                            <td><label><b>{{ trans('common.show') }} </b></label>: {{$reservation->pass->Title or ''}}   </td>
                            @endif
                            <td>
                                <label>
                                    <b>Status:</b>
                                </label>
                                @if($reservation->finished === 1)
                                    Finished
                                @elseif($reservation->finished === 0 && $reservation->cancelled_date === null) 
                                    Unfinished
                                @elseif($reservation->finished === 0 && $reservation->cancelled_date !== null)
                                    Cancelled
                                @else 
                                    Unknown status
                                @endif
                            </td>

                        </tbody>
                    </table>
        </div>
        </div>