@if($reservation)
    <table width="900" border="0" cellspacing="0" cellpadding="0">
            <tr><td colspan="2" align="center" bgcolor="#3729ff" style="color:#FFF"><h3>&emsp; {{ trans('common.final') }}   {{ trans('common.price') }}</h3></td>
          </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>

            @if($reservation->booking_fee > 0)
                <tr>
                    <td><b><span class="text-danger"><i class="fa fa-money" aria-hidden="true"></i> Booking Fee: </span></b></td>
                    <td><b><span class="text-danger">{{ $reservation->booking_fee }} €</span></b></td>
                </tr>
            @endif
            <tr>
                
                <td><span class="text-success"><i class="fa fa-money" aria-hidden="true"></i> Total:</span></td>
                <td><b><span class="text-success"><b>{{ $reservation->getTotalWristbandsPriceAttribute() }} </b>€</span></b></td>
            </tr>
  
    </table>
@endif