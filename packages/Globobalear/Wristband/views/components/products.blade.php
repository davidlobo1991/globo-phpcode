<div class="col-md-12 products-list" >
    <div class="row">
        <div class="col-md-8">
            <table class="table table-hover table-responsive">
                <thead>
                <tr>
                    <th>Wristband Pass:</th>
                    <th>Price</th>
                    <th>Start</th>
                    <th>End</th>
                </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>{{ $wristbandPass->title }}</td>
                        <td>{{ $wristbandPass->price }}</td>
                        <td>{{ $wristbandPass->date_start }}</td>
                        <td>{{ $wristbandPass->date_end }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-4">
            <table class="table table-hover table-responsive">
                <thead>
                <tr>
                    <th>Products:</th>
                </tr>
                </thead>

                <tbody>
                @foreach($wristbandPass->products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
