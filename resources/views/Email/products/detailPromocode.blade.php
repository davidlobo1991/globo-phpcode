@if($reservation->promocodes)
    <table width="900" border="0" cellspacing="0" cellpadding="0">
       <td colspan="6" bgcolor="#CCCCCC"><h3>&emsp; {{ trans('common.promocode') }} {{ trans('common.details') }} </h3></td>
        <tr>
            <td>Code</td>
            <td>Discount</td>
        </tr>
            <tr>
            <td>{{$reservation->promocodes->code }}</td>
            <td>{{ $reservation->promocodes->discount }} %</td>
            </tr>
    </table>
   
@endif