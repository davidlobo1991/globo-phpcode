<div class="box">
            <div class="box-body">
                <div class="col-md-6">
                    <p><b>Reservation:</b> {{ $reservation->reservation_number }}</p>
                </div>
                <div class="col-md-6">
                    <p><b>Name:</b> {{ $reservation->name }}</p>
                </div>
                <div class="col-md-6">
                    <p><b>Email:</b> {{ $reservation->email }}</p>
                </div>
                <div class="col-md-6">
                    <p><b>Pass :</b> {{ $reservation->pass->title or '-'}}</p>
                </div>
                 <div class="col-md-6">
                    <p><b>Pack :</b> {{ $reservation->pack->title or '-'}}</p>
                </div>
                  <div class="col-md-6">
                    <p><b>WristBand :</b>
                     @if($reservation)
                        @foreach($reservation->reservationWristbandPasses as $wbPass)
                            {{ $wbPass->passTitle or '-'}} </p>
                        @endforeach
                    @endif
                   
                </div>
                <div class="col-md-6">
                    <p><b>Channel :</b> {{ $reservation->channel->name }}</p>
                </div>
                <div class="col-md-6">
                    <p><b>Date :</b> {{ $reservation->created_at->format('d-m-Y H:i') }}</p>
                </div>
            </div>
</div>