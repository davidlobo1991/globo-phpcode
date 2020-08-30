<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-ticket"></i> {{trans('menu.home')}}</h4>
    </div>
    <div class="panel-body">
        <div class="col-md-12" >
            {!! Form::open(['route' => 'reservations-home', 'method' => 'POST']) !!}
              <div class="text-center">
                <div class="col-xs-12 col-md-3" >
                    <div class="form-group">
                    {!! Form::radio('viewType', 'availability', $viewType == 'availability') !!} Availability
                    {!! Form::radio('viewType', 'sales', $viewType == 'sales') !!} Sales
                    </div>
                </div>

                <div class="col-xs-12 col-md-3" >
                    <div class="form-group">
                        {!! Form::select('products', $products,  $request->products, ['id' => 'products', 'class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="col-xs-12  col-md-4 input-daterange" id="datepicker">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::text('dateStart', !isset($request->dateStart) ? $startDate : null, ['id' => 'dateStart', 'class' => 'form-control','readonly'=> true]) !!}
                        </div>  
                    </div> 
                   
                    <div class="col-xs-12  col-md-6"> 
                        <div class="form-group">
                            {!! Form::text('dateEnd',  $request->dateEnd, ['id' => 'dateEnd', 'class' => 'form-control','readonly'=> true]) !!}
                        </div>
                    </div>
                
                </div>
                <div class="col-xs-12  col-md-2"> 
                        <div class="form-group">
                        {!! Form::submit('Send', ['class' => 'btn btn-success form-control']) !!}
                        </div>
                </div>
                </div>
                {!! Form::close() !!}
         </div>   
    </div>
</div>


@push('scripts')
<script type="text/javascript">
$(function () {
    $('.input-daterange').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    format: "dd-mm-yyyy",
    todayHighlight: true,
    clearBtn: true,
    todayBtn: "linked",
    });
   
});
</script>
@endpush