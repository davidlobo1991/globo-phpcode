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

            <div class="col-md-12 col-xs-12  input-daterange" id="datepicker">
                <div class="col-md-6">

                    <div class="form-group">
                        {!! Form::label('date', 'Date Start')!!}
                        {!! Form::text('date_start', !isset($request->dateStart) ? $startDate : null, ['id' => 'date_start', 'class' => 'form-control','readonly'=> true]) !!}
                    </div>
                </div>

                <div class="col-xs-12  col-md-6">
                    <div class="form-group">
                        {!! Form::label('date', 'Date End')!!}
                        {!! Form::text('date_end',  $request->dateEnd, ['id' => 'date_end', 'class' => 'form-control','readonly'=> true]) !!}
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
                    {{-- Agrega las lineas de los productos --}}
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
                    {{-- Agrega las lineas de los productos --}}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')

<script src="{{ url('js\internal\packsCrud.js') }}"></script>

<script type="text/javascript">
    let qty = 0;
    let qtyShows = 0;

    const GENERATELIST = '{{ route('packs::packs.generateList' )}}';
    const GENERATE_LIST_SHOWS_PACK = '{{ route('packs::packs.generate-list-shows-pack' )}}';
    const LIST_SEAT_TYPES = '{{ route('packs::packs.list-seat-types' )}}';
    const LIST_SHOW_SEAT_TYPES = '{{ route('packs::packs.list-show-seat-types' )}}';

    $(document).ready(function() {
        $('#addLine').trigger('click');
    })

    $(document).ready(function() {
        $('#addLineShows').trigger('click');
    })
</script>
@endpush
