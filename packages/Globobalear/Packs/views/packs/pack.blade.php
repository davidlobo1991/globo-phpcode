<div class="row js-show-container">
    <div class="row parent">
        <div class="col-md-2" style="margin-bottom: 15px;">
            <button class="btn btn-danger deleteLine"><i class="fa fa-times"></i></button>
        </div>
    </div>


    <div class="col-md-4">
        <div class="form-group{{ $errors->has('products') || $errors->has('products') ? ' has-error' : '' }} showSelector">
            {!! Form::label('products', 'Product')!!}

            {!! Form::select("products[{$el}][id]", $products, $product_id ?? null, ['class' => "form-control js-show-select", 'id' => 'products','required'=>true, 'data-num' => $el]) !!}
        </div>
    </div>

    <div class="col-md-8">
        <div class="o-product__seat-types js-seat-types {{ $errors->has('seatypes') || $errors->has('seatypes') ? 'has-error' : '' }}">

            @isset($product_id)
                @include('packs::packs.seattypes')
            @endif
        </div>
    </div>
</div>
