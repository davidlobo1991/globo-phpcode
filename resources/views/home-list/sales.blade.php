<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-thumbs-up"></i> {{trans('common.sales')}}</h4>
    </div>
    <div class="panel-body">
        <div class="col-md-12" >

        <div class="text-center">
        <div class="overflow-scroll">
            <table class="table-list" id='reservations-home-list-table'>
                            <thead>
                                    <tr class='header-block'>
                                        <th colspan='3' ></th>
                                        @foreach($passesSeller as $seller)
                                            <th colspan='{{ count($seatType) +1 }}'  style="width: 25%;">
                                                {{ $seller->name }}
                                            </th>
                                        @endforeach
                                    </tr>
                                    <tr class='sub-header-block'>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Show</th>
                                        @foreach($passesSeller as $seller)
                                         <th>TOT</th>
                                            @foreach($seatType as $seat)
                                            <th>{{ $seat->acronym }}</th>
                                            @endforeach
                                        @endforeach
                                    </tr>
                            </thead>
                            
                            <tbody>
                             @if(!$passes->isEmpty())
                            {{-- bucle de Pirates Passes Seller--}}
                                @foreach($passes as $pass)
                                
                                    @if ($pass->is_canceled)
                                        <tr class="canceled-pass">
                                        <td><a href="/booking/{{$pass->id}}"> {{ $pass->datetime->format('D, M d') }}</a></td> 
                                        <td><a href="/booking/{{$pass->id}}"> {{ $pass->datetime->format('H:i') }}</a></td> 
                                        <td><a href="/booking/{{$pass->id}}"> {{$pass->productName}}</a></td>
                                        {{-- bucle de Pirates Passes Seller--}}
                                        <td class='first-block-item passes-quantity @if($pass->getAvailabilitySum()==0) unavailable-passes @else available-passes @endif'>{{ $pass->getAvailabilitySum() }}</td>
                                        @foreach($passesSeller as $passSeller) 
                                            @foreach($pass->getAvailability('', $passSeller->id) as $Overview)
                                            @if($Overview->seats_sold==0)
                                            <td class="first-block-item passes-quantity unavailable-passes">
                                            <a class="passes-sellers-link" href="{{ route('reservations.create', ['id' => 1 ,'show' => $pass->show_id,'pass' => $pass->id]) }}">
                                            {{ $Overview->seats_sold }}
                                            </a>
                                             </td>
                                            @elseif ($Overview->seats_sold < $global->amber_trigger)
                                            <td class="first-block-item passes-quantity dangerous-availability">
                                            <a class="passes-sellers-link" href="{{ route('reservations.create', ['id' => 1 ,'show' => $pass->show_id,'pass' => $pass->id]) }}">
                                            {{ $Overview->seats_sold }} 
                                            </a>
                                            </td>
                                            @else
                                            <td class="first-block-item passes-quantity available-passes">
                                            <a class="passes-sellers-link" href="{{ route('reservations.create', ['id' => 1 ,'show' => $pass->show_id,'pass' => $pass->id]) }}">
                                            {{ $Overview->seats_sold }} 
                                            </a>
                                            </td>
                                            @endif
                                        @endforeach
                                        @endforeach
                                        {{-- bucle de Pirates Passes Seller--}} 
                                        
                                    @elseif (!$pass->on_sale)
                                        <tr class="not-on-sale">
                                        <td><a href="/booking/{{$pass->id}}"> {{ $pass->datetime->format('D, M d') }}</a></td> 
                                        <td><a href="/booking/{{$pass->id}}"> {{ $pass->datetime->format('H:i') }}</a></td> 
                                        <td><a href="/booking/{{$pass->id}}"> {{$pass->Title}} {{ $pass->FormattedDate }}</a></td> 
                                        {{-- bucle de Pirates Passes Seller--}}
                                        <td class='first-block-item passes-quantity @if($pass->getAvailabilitySalesSum()==0) unavailable-passes @else available-passes @endif'>{{ $pass->getAvailabilitySalesSum() }}</td>
                                        @foreach($passesSeller as $passSeller) 
                                            @foreach($pass->getAvailability('', $passSeller->id) as $Overview)
                                            @if($Overview->seats_sold==0)
                                            <td class="first-block-item passes-quantity unavailable-passes">
                                            <a class="passes-sellers-link" href="{{ route('reservations.create', ['id' => 1 ,'show' => $pass->show_id,'pass' => $pass->id]) }}">
                                            {{ $Overview->seats_sold }}
                                            </a>
                                             </td>
                                            @elseif ($Overview->seats_sold < $global->amber_trigger)
                                            <td class="first-block-item passes-quantity dangerous-availability">
                                            <a class="passes-sellers-link" href="{{ route('reservations.create', ['id' => 1 ,'show' => $pass->show_id,'pass' => $pass->id]) }}">
                                            {{ $Overview->seats_sold }}
                                            </a>
                                             </td>
                                            @else
                                            <td class="first-block-item passes-quantity available-passes">{{ $Overview->seats_sold }} </td>
                                            @endif
                                        @endforeach
                                        @endforeach
                                        {{-- bucle de Pirates Passes Seller--}} 
                                        
                                    @elseif  ($pass->hasThisDayPassed())
                                        <tr class="day-passed">
                                        <td><a href="/booking/{{$pass->id}}"> {{ $pass->datetime->format('D, M d') }}</a></td> 
                                        <td><a href="/booking/{{$pass->id}}"> {{ $pass->datetime->format('H:i') }}</a></td> 
                                        <td><a href="/booking/{{$pass->id}}"> {{$pass->Title}} {{ $pass->FormattedDate }}</a></td> 
                                        {{-- bucle de Pirates Passes Seller--}}
                                         <td class='first-block-item passes-quantity @if($pass->getAvailabilitySalesSum()==0) unavailable-passes @else available-passes @endif'>{{ $pass->getAvailabilitySalesSum() }}</td>
                                        @foreach($passesSeller as $passSeller) 
                                            @foreach($pass->getAvailability('', $passSeller->id) as $Overview)
                                            @if($Overview->seats_sold==0)
                                            <td class="first-block-item passes-quantity unavailable-passes">
                                            <a class="passes-sellers-link" href="{{ route('reservations.create', ['id' => 1 ,'show' => $pass->show_id,'pass' => $pass->id]) }}">
                                            {{ $Overview->seats_sold }} 
                                            </a>
                                            </td>
                                            @elseif ($Overview->seats_sold < $global->amber_trigger)
                                            <td class="first-block-item passes-quantity dangerous-availability">
                                            <a class="passes-sellers-link" href="{{ route('reservations.create', ['id' => 1 ,'show' => $pass->show_id,'pass' => $pass->id]) }}">
                                            {{ $Overview->seats_sold }} 
                                            </a>
                                            </td>
                                            @else
                                            <td class="first-block-item passes-quantity available-passes">
                                            <a class="passes-sellers-link" href="{{ route('reservations.create', ['id' => 1 ,'show' => $pass->show_id,'pass' => $pass->id]) }}">
                                            {{ $Overview->seats_sold }} 
                                            </a>
                                            </td>
                                            @endif
                                        @endforeach
                                        @endforeach
                                        {{-- bucle de Pirates Passes Seller--}} 
                                    @else
                                        <tr class="on-sale">
                                        <td><a href="/booking/{{$pass->id}}"> {{ $pass->datetime->format('D, M d') }}</a></td> 
                                        <td><a href="/booking/{{$pass->id}}"> {{ $pass->datetime->format('H:i') }}</a></td> 
                                        <td><a href="/booking/{{$pass->id}}"> {{$pass->Title}} {{ $pass->FormattedDate }}</a></td> 
                                        {{-- bucle de Pirates Passes Seller--}}
                                      
                                         <td class='first-block-item passes-quantity @if($pass->getAvailabilitySalesSum()==0) unavailable-passes @else available-passes @endif'>{{ $pass->getAvailabilitySalesSum() }}</td>
                                        @foreach($passesSeller as $passSeller) 
                                            @foreach($pass->getAvailability('', $passSeller->id) as $Overview)
                                            @if($Overview->seats_sold==0)
                                            <td class="first-block-item passes-quantity unavailable-passes">
                                            <a class="passes-sellers-link" href="{{ route('reservations.create', ['id' => 1 ,'show' => $pass->show_id,'pass' => $pass->id]) }}">
                                            {{ $Overview->seats_sold }} 
                                            </a>
                                            </td>
                                            @elseif ($Overview->seats_sold < $global->amber_trigger)
                                            <td class="first-block-item passes-quantity dangerous-availability">
                                            <a class="passes-sellers-link" href="{{ route('reservations.create', ['id' => 1 ,'show' => $pass->show_id,'pass' => $pass->id]) }}">
                                            {{ $Overview->seats_sold }} 
                                            </a>
                                            </td>
                                            @else
                                            <td class="first-block-item passes-quantity available-passes">
                                            <a class="passes-sellers-link" href="{{ route('reservations.create', ['id' => 1 ,'show' => $pass->show_id,'pass' => $pass->id]) }}">
                                            {{ $Overview->seats_sold }} 
                                            </a>
                                            </td>
                                            @endif
                                        @endforeach
                                        @endforeach
                                        {{-- bucle de Pirates Passes Seller--}} 

                                    @endif
                                    {{-- Link asignación de asientos --}}
                                        @if(!$pass->hasThisDayPassed())
                                            <td style='background-color: white !important;'>
                                                    <a href="{{ route('passes.edit', $pass->id) }}" class='btn btn-info'> <i class="fa fa-arrow-right"></i>
                                                    </a>
                                                </td>
                                        @endif
                                    {{-- Link asignación de asientos --}}


                                @endforeach
                            {{-- fin bucle --}}
                             @endif
                            
                            </tbody>

                            <tr>
                                <td colspan='50' class="text-center">
                                    <ul class='colors-legend text-center'>
                                        <li><span class='on-sale-legend'></span> On Sale</li>
                                        <li><span class='cancelled-pass-legend'></span> Not on sale / Cancelled</li>
                                        <li><span class='day-passed-legend'></span> Previous</li>
                                      
                                    </ul>
                                </td>
                            </tr>

            </table>
        </div>
        </div>

</div>
</div>
</div>