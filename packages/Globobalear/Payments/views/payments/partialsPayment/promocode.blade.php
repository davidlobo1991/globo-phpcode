
@if(isset($paid->reservation->promocodes) && !empty($paid->reservation->promocodes))
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
                    <td>{{$paid->reservation->promocodes->code or '' }}</td>
                    <td>{{ $paid->reservation->promocodes->discount or '' }} %</td>
                </tr>

                </tbody>

            </table>
        </div>
    </div>
@endif
