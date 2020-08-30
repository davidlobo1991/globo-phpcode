<div class="countMenus col-md-12" id="menu{{ $id }}">
    <hr>

    <div class="row">
        <div class="form-group col-md-12">
            {!! Form::hidden("menu[{$id}][menu_id]", $menu->id, ['class' => 'menuId']) !!}
            <b>Menu: </b> {{ $menu->name }} | 
           
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-7">
            @if(isset($menu))
                 <b>Dishes:</b> 
            @foreach($menu->dishes as $dishes)
            <b>{{ $dishes->name }} </b>
            <p>  {{ $dishes->description_allergens }} </p>
            @endforeach
            @endif
        </div>

        <div class="col-md-3">
            @if(isset($menus))
           
                {!! Form::number("menu[{$id}][quantity]", $menus->quantity, ['class' => 'form-control quantity', 'placeholder' => 'Quantity', 'min' => '0']) !!}
            @else
                {!! Form::number("menu[{$id}][quantity]", null, ['class' => 'form-control quantity', 'placeholder' => 'Quantity', 'min' => '0']) !!}
            @endif
        </div>

        <div class="form-group col-md-2">
            <div class="btn btn-block btn-danger" id="deleteMenu">
                <i class="fa fa-trash"></i>
            </div>
        </div>
    </div>

    <div class="row infoPickup"></div>

</div>
