<div class="panel panel-default" id="app">
    <div class="panel-heading">
        <h4><i class="fa fa-calendar-o"></i> Pack Info</h4>
    </div>
    <div class="panel-body">

    <div class="col-md-12 col-xs-12">
        <div class="form-group{{ $errors->has('title') || $errors->has('title') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('title', NULL, ['class' => 'form-control', 'placeholder' => 'Title*', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('title')" class="help-inline text-danger">@{{ errors.first('title') }}</div>
        </div>

        <div class="form-group{{ $errors->has('acronym') || $errors->has('acronym') ? ' has-error' : '' }}">
            <float-label>
                {!! Form::text('acronym', NULL, ['class' => 'form-control', 'placeholder' => 'Acronym*', 'v-validate' => "'required'"]) !!}
            </float-label>
            <div v-if="errors.has('acronym')" class="help-inline text-danger">@{{ errors.first('acronym') }}</div>
        </div>

         {{-- <div class="form-group{{ $errors->has('ticket_type') || $errors->has('ticket_type') ? ' has-error' : '' }}">
            {!! Form::label('ticket_type', 'Tickets Types')!!}

            {!! Form::select("ticket_type", $tickettype, $pack->packline->first() ? $pack->packline->first()->ticket_type_id : ($pack->packlinePirates->first() ? $pack->packlinePirates->first()->ticket_type_id : null), ['class' => "form-control",'id' => 'ticket_type','required'=>'required']) !!}
        </div> --}}

        <div class="col-md-12 col-xs-12  input-daterange" id="datepicker">
                <div class="col-md-6">
                 <div class="form-group">
                    {!! Form::label('date', 'Date Start')!!}
                        {!! Form::text('date_start', $pack->date_start->format('d-m-Y'), ['id' => 'date_start', 'class' => 'form-control','readonly'=> true,'required'=>'required']) !!}
                    </div>
                </div>

                <div class="col-xs-12  col-md-6">
                    <div class="form-group">
                    {!! Form::label('date', 'Date End')!!}
                        {!! Form::text('date_end',  $pack->date_end->format('d-m-Y'), ['id' => 'date_end', 'class' => 'form-control','readonly'=> true,'required'=>'required']) !!}
                    </div>
                </div>
         </div>

    </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-calendar-o"></i> Add Products</h4>
    </div>
    <div class="panel-body">

        <div class="col-md-12 col-xs-12">

            <div class="box-body">

                <div class="col-md-12">
                    <button class="btn btn-primary" id="addLine">Add Line Products</button>
                    <hr>
                </div>
                <div class="col-md-12" id="lines">
                    @if(!$pack->packline->isEmpty())
                        @foreach($pack->packline->groupBy('product_id') as $packlines)
                            {{-- Starts with 1 --}}
                            @include('packs::packs.pack', [
                                'el' => $loop->iteration,
                                'seatypes' => $packlines->first()->product->seatTypes,
                                'ticketTypes' => $packlines->first()->product->ticketTypes,
                                'product_id' => $packlines->first()->product_id,
                                'packlines' => $packlines
                            ])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-calendar-o"></i> Add Shows</h4>
    </div>
    <div class="panel-body">
        <div class="col-md-12 col-xs-12">
            <div class="box-body">
                <div class="col-md-12">
                    <button class="btn btn-primary" id="addLineShows">Add Line Shows</button>
                <hr>
                </div>
                <div class="col-md-12" id="linesshows">
                    @if(!$pack->packlinePirates->isEmpty())
                        @foreach($pack->packlinePirates->groupBy('show_id') as $packlinesPirates)
                            {{-- Starts with 1 --}}
                            @include('packs::packs.showspack', [
                                'elpirates' => $loop->iteration,
                                'seatypes' => $packlinesPirates->first()->show->seatTypes,
                                'ticketTypes' => $packlinesPirates->first()->show->ticketTypes,
                                'show_id' => $packlinesPirates->first()->show_id,
                                'packlinesPirates' => $packlinesPirates
                            ])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')

<script src="{{ url('js\internal\packsCrud.js') }}"></script>

<script type="text/javascript">
    let qty = {{ $pack->packline->count() + 1 }};
    let qtyShows = {{ $pack->packlinePirates->count() + 1 }};

    const GENERATELIST = '{{ route('packs::packs.generateList' )}}';
    const GENERATE_LIST_SHOWS_PACK = '{{ route('packs::packs.generate-list-shows-pack' )}}';
    const LIST_SEAT_TYPES = '{{ route('packs::packs.list-seat-types' )}}';
    const LIST_SHOW_SEAT_TYPES = '{{ route('packs::packs.list-show-seat-types' )}}';
</script>
@endpush
