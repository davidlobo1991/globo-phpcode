 @if(isset($reservation->reservationMenu) && !empty($reservation->reservationMenu))
 @if(!$reservation->reservationMenu->isEmpty())   
    <div class="panel panel-primary">
    <div class="panel-heading">
    <h4> <i class="fa fa-cutlery"></i>
                {{ trans('common.menu') }} {{ trans('common.details') }}  </h4>
        </div>
    <div class="panel-body">

    <table class="table table-hover table-responsive">
       <thead>
        <tr>
            <td>Menu</td>
            <td>Dishes</td>
            <td>Quantity</td>
          
        </tr>
        </thead>
        <tbody>
        @foreach($reservation->reservationMenu as $menu)
            
                <tr>
                <td>{{ $menu->MenuTitle }}</td>
                <td>
                
                @foreach($menu->menu->dishes as $dishes)
               
                {{ $dishes->name }}
              
                 @endforeach
                </td>
               
                <td>{{ $menu->quantity }}</td>
               
            </tr>
        @endforeach
        </tbody>
       
    </table>
    </div>
    </div>
@endif
@endif