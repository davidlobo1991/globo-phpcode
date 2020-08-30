<div class="row js-show-pirates-container">
    <div class="row parent">
        <div class="col-md-2" style="margin-bottom: 15px;">
            <button class="btn btn-danger deleteLine"><i class="fa fa-times"></i></button>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group{{ $errors->has('showspirates') || $errors->has('showspirates') ? ' has-error' : '' }} showSelector">
            {!! Form::label('shows', 'Shows')!!}

            {!! Form::select("showspirates[{$elpirates}][id]", $shows, $show_id ?? null, ['class' => "form-control js-show-pirates-select", 'id' => 'shows', 'required' => true, 'data-num' => $elpirates]) !!}
        </div>
    </div>

    <div class="col-md-8">
        <div class="o-product__seat-types js-show-seat-types {{ $errors->has('seatypes') || $errors->has('seatypes') ? 'has-error' : '' }}">

            @isset($show_id)
                @include('packs::packs.showsseattypes')
            @endif
        </div>
    </div>
</div>
