
@if(isset($paid->reservation->reservationWristbandPasses) && !empty($paid->reservation->reservationWristbandPasses))
    @if ($paid->reservation->reservationWristbandPasses->count())
        <div class="panel panel-default">
            <div class="panel-heading">
                @foreach($paid->reservation->reservationWristbandPasses as $item)
                    <h4><i class="fa fa-ticket"></i> {{$item->title}}</h4>
            </div>
            <div class="panel-body">
                <table class="table table-hover table-responsive text-center">
                    <thead class="thead-default">
                    <tr>
                        <td><b>Date Start</b></td>
                        <td><b>Date End</b></td>
                        <td><b>Quantity</b></td>
                        <td><b>Price</b></td>
                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td>{{$item->date_start}}</td>
                        <td> {{$item->date_end}}</td>
                        <td> {{$item->pivot->quantity}}</td>
                        <td> {{$item->price}}</td>
                    </tr>

                    </tbody>
                </table>
                @endforeach
            </div>
        </div>

    @endif
@endif
