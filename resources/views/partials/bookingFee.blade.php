 <div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-user"></i> Booking Fee</h4>
    </div>
    <div class="panel-body">
        <div class="form-group col-md-12">

        <div class="col-md-12" >
            <div class="form-group">
                {!! Form::checkbox('booking_fee', $global->booking_fee, isset($reservation) 
                && !empty($reservation->booking_fee) && $reservation->booking_fee != "0.00",['class' => 'iCheck', 'id' => 'booking_fee']) !!}
              + {{ $global->booking_fee }} €
            </div>
        </div>

    </div>
</div>
</div>
