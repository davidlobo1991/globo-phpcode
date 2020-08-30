

<div class="row">
    <div class="col-md-12">
        <div class="panel-group">
            <div class="panel panel-info">
                <div class="panel-heading">
                <h3 class="panel-title ">{{$pack->title}}</h3>
                </div>
                    <div class="container-fluid" style="padding: 15px 15px 0px 15px;">
                    <div class=" panel panel-info">
                        <div class="panel-body">
                        <label class="col-md-4 control-label" for="textinput"><i class="fa fa-ticket"></i>
                        @if(!$packline->isEmpty())
                        TicketType: {{$packline->first()->TitleTicketType}}
                        {!! Form::hidden('tickettypes', $packline->first()->ticket_type_id ) !!}
                        @else
                        TicketType: {{$packlinePirates->first()->TitleTicketType}}
                        {!! Form::hidden('tickettypes', $packlinePirates->first()->ticket_type_id ) !!}
                        @endif
                        </label>
                        <label class="col-md-4 control-label" for="textinput"><i class="fa fa-clock-o"></i> Date Start: {{$pack->date_start}}</label>
                        <label class="col-md-4 control-label" for="textinput"><i class="fa fa-clock-o"></i> Date End: {{$pack->date_end}}</label>
                        </div>
                    </div>
                    </div>

        @if(!$packline->isEmpty())
        <div class="panel-body" style="padding: 15px 0px 0px 15px;">
            <fieldset>
                <legend>Line Products</legend>
                    <div class="form-group">
                         <div class="col-md-12">

                            @foreach($packline as $item)

                            {!! Form::hidden('el[]', $loop->iteration) !!}
                            {!! Form::hidden('products['.$loop->iteration.']', $item->product_id  ) !!}
                            {!! Form::hidden('seattypes['.$loop->iteration.']', $item->seat_type_id ) !!}
                            {!! Form::hidden('tickettypes', $item->ticket_type_id ) !!}
                            {!! Form::hidden('price['.$loop->iteration.']', $item->price  ) !!}
                            {{-- Starts with 1 --}}
                                <div class="row">
                                <div class="col-md-6">
                                <div class="row form-group">
                                <h4><i class="fa fa-shopping-cart"></i>{{$item->TitleProduct }}: </h4>

                                <div><i class="fa fa-hashtag"></i> SeatType:<b> {{$item->TitleSeaType }}</b></div>
                                <div><i class="fa fa-money"></i> Price :<b>{{$item->price }} € </b></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                    <div class="form-group">

                                    {!! Form::label('pass', 'Pass')!!}
                                    <select name='pass[{{ $loop->iteration }}]' id="pass" class="form-control select select-pass">
                                            @foreach($item->passes as $pass)

                                                    <option data-max="{{$pass->seattypes->where('id', $item->seat_type_id)->first()->pivot->FreeSeats}}" value="{{$pass->id}}"
                                                    @if(isset($reservation->reservationPacks))
                                                    @if($reservation->reservationPacks->where('pass_id',$pass->id)->first()['pass_id'] == $pass->id)
                                                    selected
                                                    @endif
                                                    @endif
                                                    >
                                                    {{$pass->FormattedDate}} - FreeSeats ({{$pass->seattypes->where('id', $item->seat_type_id)->first()->pivot->FreeSeats}})
                                                    </option>
                                            @endforeach
                                    </select>

                                    </div>
                            </div>
                            </div>
                            <hr>
                            @endforeach

                            </div>

                        </div>

                        </fieldset>

        </div>
         @endif
         @if(!$packlinePirates->isEmpty())
        <div class="panel-body" style="padding: 0px 0px 0px 15px;">
            <fieldset>
                <legend>Line Shows</legend>
                <div class="form-group">
                <div class="col-md-12">

                    @foreach($packlinePirates as $item)
                        {!! Form::hidden('elpirates[]', $loop->iteration) !!}
                        {!! Form::hidden('showspirates['.$loop->iteration.']', $item->show_id  ) !!}
                        {!! Form::hidden('seattypespirates['.$loop->iteration.']', $item->seat_type_id ) !!}
                        {!! Form::hidden('tickettypespirates['.$loop->iteration.']', $item->ticket_type_id ) !!}
                        {!! Form::hidden('pricepirates['.$loop->iteration.']', $item->price  ) !!}
                        {{-- Starts with 1 --}}
                    <div class="row">
                    <div class="col-md-6">
                    <div class="form-group">
                    <h4><i class="fa fa-shopping-cart"></i> {{$item->TitleShow }}: </h4>
                    <div><i class="fa fa-hashtag"></i> SeatType: <b>{{$item->TitleSeaType }}</b></div>
                    <div><i class="fa fa-money"></i> Price : <b>{{$item->price }} €</b></div>
                    </div>
                    </div>

                    <div class="col-md-6">
                    <div class="form-group">
                    {!! Form::label('pass', 'Pass')!!}

                    <select  name='passpirates[{{ $loop->iteration }}]' id="passpirates" class="form-control select select-pass">
                            @foreach($item->passes as $pass)
                                    <option data-max="{{$pass->getTotalSeats(1,$pass->id,$item->seat_type_id)}}" value="{{$pass->id}}"

                                    @if(isset($reservation->reservationPacksPirates))
                                    @if($reservation->reservationPacksPirates->where('pass_id',$pass->id)->first()['pass_id'] == $pass->id)
                                    selected
                                    @endif
                                    @endif
                                    > {{$pass->FormattedDate}} - FreeSeats ({{$pass->getTotalSeats(1,$pass->id,$item->seat_type_id)}})</option>
                            @endforeach
                    </select>

                    </div>
                    </div>
                    </div>
                    <hr>
                    @endforeach

                </div>
                </div>
            </fieldset>

        </div>
        @endif


        
       
        @if($packlineWristbands)
        <div class="panel-body" style="padding: 0px 0px 0px 15px;">
            <fieldset>
                <legend>Line Wristband</legend>
                <div class="form-group">
                <div class="col-md-12">
                   {!! Form::hidden('wristband_passes_id', $packlineWristbands->wristband_passes_id  ) !!}
                   {!! Form::hidden('pricewristband', $packlineWristbands->price  ) !!}
                    <div class="hidden">
                    <select  name='wristbandavailable' id="wristbandavailable" class="form-control select select-pass">
                      <option data-max="{{ $packlineWristbands->wristbands->quantity - $packlineWristbands->FilledSeats}}" value="{{ $packlineWristbands->wristbands->quantity - $packlineWristbands->FilledSeats}}">
                    </select>
                    </div>
                        {{-- Starts with 1 --}}
                    <div class="row">
                    <div class="col-md-6">
                    <div class="form-group">
                    <h4><i class="fa fa-shopping-cart"></i> {{$packlineWristbands->wristbands->title }}: </h4>
                    <div><i class="fa fa-hashtag"></i> Free: <b>{{ $packlineWristbands->wristbands->quantity - $packlineWristbands->FilledSeats}}</b></div>
                    <div><i class="fa fa-money"></i> Price : <b>{{$packlineWristbands->price }} €</b></div>
                    </div>
                    </div>

                    
                    </div>
                    <hr>
                   

                </div>
                </div>
            </fieldset>

        </div>
        @endif

    </div>
</div>



    <script type="text/javascript">
      $('select.select').select2({
        placeholder: 'Select a:'
      });

    </script>


