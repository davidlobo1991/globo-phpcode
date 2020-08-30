 @if(isset($payments) && !empty($payments))
 @if(!$payments->isEmpty())   
        
        <table class="table table-striped table-hover table-condensed table-responsive">
        <tr><td> <h4>Payments</h4></td></tr>
        <thead class="thead-default">
            <tr>
                <td class="col-md-2">Reservation </td>
                <td class="col-md-2">Phone </td>
                <td class="col-md-2">{{ trans('common.show') }} </td>
                <td class="col-lg-auto">Paid </td>
                <td class="col-lg-auto">Revenue</td>
                <td class="col-lg-auto">Return </td>
                <td class="col-lg-auto">Commision </td>
                <td class="col-md-2">Date </td>
                
            </tr>
        </thead>
            <tbody>
            @foreach($payments as $paid)
                
                    <tr>
                        <td><a href="{{ route('reservations.product', $paid->reservation_id) }}">
                        {{ $paid->reservation_number }} - {{ $paid->customers_name }}
                        </a></td>
                        <td>{{ $paid->customers_phone }} </td>
                        <td>{{ $paid->name_reservation }} </td>
                        <td>{{ $paid->total }} </td>
                        <td>@if($paid->totalcomision){{ $paid->totalcomision }}  @else  @endif  </td>
                        <td>@if($paid->comisionpirates){{ $paid->comisionpirates }}  @else  @endif </td>
                        <td>@if($paid->commission){{ $paid->commission }} % @else  @endif</td>
                        <td>{{ $paid->created_at->format('d-m-Y H:i') }} </td>
                        
                        
                    </tr>
                
            @endforeach
        </tbody>

        
        </table>
       
   
@endif
@endif
