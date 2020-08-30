<div>
    <div class="row">
        <div class="col-md-2" style="margin-bottom: 15px;">
            <button class="btn btn-danger deleteLine"><i class="fa fa-times"></i></button>
        </div>
    </div>

    {!! Form::hidden('el[]', $el) !!}

    <div class="row" style="margin-bottom: 15px;">
        <div class="col-md-2">
            {!! Form::text("from[{$el}]", null, ['class' => 'form-control from','readonly'=> true, 'required'=> true]) !!}
        </div>
        <div class="col-md-2">
            {!! Form::text("to[{$el}]", null, ['class' => 'form-control to','readonly'=> true, 'required'=> true]) !!}
        </div>
        <div class="col-md-2">
            {!! Form::text("hour[{$el}]", null, ['class' => 'form-control time', 'required'=> true]) !!}
        </div>
        <div class="col-md-3">
            {!! Form::select("product[{$el}]", $products, null, ['class' => 'form-control showSelector', 'data-el' => $el, 'required'=> true]) !!}
        </div>
    </div>

    <div class="row" style="margin-bottom: 25px;">
        <div class="flex col-md-12">
            @foreach($days as $i => $day)
                <div class="flex-item">
                    {!! Form::checkbox("days[{$el}][]", $i, null, ['class' => 'iCheck']) !!} {{ $day }}
                </div>
            @endforeach
        </div>
    </div>

    <div class="row addTable" style="margin-bottom: 15px;"></div>
    <hr>
</div>

<script>
    $(document).ready(function () {
        $('.from').datepicker({
            autoclose: true,
            startDate: '0d',
            todayHighlight: true,
            format: 'dd/mm/yyyy',
            clearBtn: true,
            todayBtn: "linked",
            keyboardNavigation: false,
        }).on('changeDate', function (selected) {
            var startDate = new Date(selected.date.valueOf());
            $('.to').datepicker('setStartDate', startDate);

            $('.to').datepicker('setEndDate', end);
        }).on('clearDate', function (selected) {
            $('.to').datepicker('setStartDate', null);
        })

        $('.to').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd/mm/yyyy'
        }).on('changeDate', function (selected) {
            var endDate = new Date(selected.date.valueOf());
            $('.from').datepicker('setEndDate', endDate);
        }).on('clearDate', function (selected) {
            $('.from').datepicker('setEndDate', null);
        })

        $('.time').timepicker({
            minuteStep: 5,
            showInputs: false,
            showMeridian: false,
        })

        $('.iCheck').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%'
        })
    })
</script>
