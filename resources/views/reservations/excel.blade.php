<html>
<table class="table">
    <thead>
    <tr><td class="cell"><h2>PIRATES MALLORCA</h2></td></tr>
    <tr><td class="cell"><h3>Reservation</h1></td></tr>
    <tr style="background-color: #CCC;">
        <th class="cod-row">Ref.</th>
        <th>Product</th>
        <th>Name</th>
        <th>Surname</th>
        <th>Company</th>
        <th>Email</th>
        <th>Telephone</th>
        <th>Promocode</th>
        <th>Promocode %</th>
        <th>Category</th>
        <th>TOT</th>
        <th>ADU</th>
        <th>CHD</th>
        <th>INF</th>
        <th>Price</th>
        <th>Date</th>
        <th>Operator</th>
    </tr>
    </thead>
    <tbody>
    @foreach($reservations as $reservation)
        <tr>
            <td>
                {{ $reservation->reservation_number }}
            </td>
            <td>
                {{ $reservation->name_reservation }}
            </td>
            <td>
                {{ $reservation->name }}
            </td>
            <td>
                {{ $reservation->surname }}
            </td>
             <td>
                {{ $reservation->company  }}
            </td>
            <td>{!! $reservation->email !!}</td>
            <td>{!! $reservation->phone !!}</td>
            <td>{!! $reservation->promocode_code !!}</td>
            <td>{!! $reservation->promocode_discount !!}</td>
            <td>
                {{ $reservation->reservationTickets->first() ? $reservation->reservationTickets->first()->seatTypeTitle : '' }}
            </td>
            <td>{!! $reservation->TOT !!}</td>
            <td>{!! $reservation->ADU !!}</td>
            <td>{!! $reservation->CHD !!}</td>
            <td>{!! $reservation->INF !!}</td>
            <td>{!! $reservation->total_price !!}</td>
            <td>{{ $reservation->created_at->format('d-m-Y H:i') }}</td>
            <td>{!! $reservation->channel !!}</td>
        </tr>
    @endforeach
    </tbody>

</table>
</html>
