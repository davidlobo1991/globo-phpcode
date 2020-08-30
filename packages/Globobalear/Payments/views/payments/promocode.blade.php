
@if(isset($reservation->promocodes) && !empty($reservation->promocodes))
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4> <i class="fa fa-cutlery"></i>
                {{ trans('common.promocode') }} {{ trans('common.details') }}  </h4>
        </div>
        <div class="panel-body">

            <table class="table table-hover table-responsive">
                <thead>
                <tr>
                    <td>Code</td>
                    <td>Discount</td>
                </tr>
                </thead>
                <tbody>

                <tr>
                    <td>{{$reservation->promocodes->code or '' }}</td>
                    <td>{{ $reservation->promocodes->discount or '' }} %</td>
                </tr>

                </tbody>

            </table>
        </div>
    </div>
@endif