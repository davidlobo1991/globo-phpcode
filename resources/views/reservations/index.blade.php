@extends('adminlte::layouts.app')

@section('contentheader_title', trans('menu.reservations'))

@section('main-content')
    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-12">

                <!-- Default box -->
                <div class="box">
                    <div class="box-header with-border">
                        <div class="o-grid">
                            <div class="o-grid__actions">
                                @foreach($passesSellers as $passSeller)
                                    @foreach($reservationstypes as $type)
                                    <a href="{{ route($type->route, ['id' => $passSeller->id]) }}"
                                       class="btn btn-primary">{{ trans('common.create') }} for {{ $type->name }}</a>
                                    @endforeach
                                @endforeach
                            </div>

                            <div class="o-grid__collapse">
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                                            title="Collapse">
                                        <i class="fa fa-minus"></i></button>
                                </div>
                            </div>

                            <form method="POST" class="o-grid__booking-search" id="bookingSearch">
                                {{ csrf_field() }}
                                <div class="c-booking-search__title">
                                    Booking search
                                    <a class="btn btn-info js-show-booking-search" @if (!empty(request()->all())) style="display: none;" @endif>
                                        <i class="fa fa-search"></i>
                                    </a>
                                    <a class="btn btn-danger js-hide-booking-search" @if (empty(request()->all())) style="display: none;" @endif>
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>

                                <div class="js-booking-search" @if (empty(request()->all())) style="display: none;" @endif>
                                    <div class="c-booking-search__fields">
                                        @include('reservations.bookingsearch')
                                    </div>

                                    <div class="c-booking-search__buttons">
                                        <button class="btn btn-warning js-clear-form" name="clear" type="button">Clear</button>

                                        <button value="search" class="btn btn-info" name="search" type="submit">Search</button>
                                    </div>
                                </div>

                                <div class="c-booking-search__excel">
                                    <button value="excel" class="btn btn-success" name="excel" type="submit">Export Excel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="box-body">
                        @include('flash::message')
                        <div class="overflow-scroll">
                        <table id="list-table" class="table table-striped table-hover table-condensed table-responsive">
                            <thead>
                            <tr>
                                <th>{{ trans('common.reservation_number') }}</th>
                                <th>{{ trans('common.type') }}</th>
                                <th>{{ trans('common.product') }}</th>
                                <th>{{ trans('common.name') }}</th>
                                <th>{{ trans('common.email') }}</th>
                                <th>{{ trans('common.channel') }}</th>

                                <th>{{ trans('common.date') }}</th>
                                <th>ADU</th>
                                <th>CHD</th>
                                <th>INF</th>
                                <th>TOT</th>

                                <th style="width: 20%">{{ trans('common.actions') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        let reservationsTable = $('#list-table').DataTable({
            processing: true,
            serverSide: true,
            columns: [
                {data: 'reservation_number'},
                {data: 'type'},
                {data: 'name_reservation'},
                {data: 'name'},
                {data: 'email'},
                {data: 'channel'},
                {data: {
                    _:    "created_at",
                    sort: "fecha"
                }, name:'created_at' },
                {data: 'ADU'},
                {data: 'CHD'},
                {data: 'INF'},
                {data: 'TOT'},

                {data: 'action', orderable: false, searchable: false}
            ],
            ajax: {
                url: '{{ route('reservations.data', request()->all()) }}',
                beforeSend: function (request) {
                    const $form = $('#bookingSearch');
                    const formValues = $form.serializeArray();

                    for (var i = 0; i < formValues.length; i++) {
                        let formValue = formValues[i];

                        request.setRequestHeader(formValue.name, formValue.value);
                    }
                }
            }
        });

        $("#bookingSearch").on('submit', function (e) {
            var val = $("button[type=submit][clicked=true]").val();

            if (val == 'search') {
                e.preventDefault();

                reservationsTable.ajax.reload();
                // window.history.pushState("", "", '{{ url()->current() }}?'+$(this).serialize());
            }

            $(this).attr('action', '{{ route('reservations.excel') }}')
        });

        $("#bookingSearch button[type=submit]").click(function() {
            $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
            $(this).attr("clicked", "true");
        });

        $('.js-clear-form').on('click', function () {
            const $form = $("#bookingSearch");

            $.each($form.find('input'), function (key, value) {
                $(value).val('');
            });

            $.each($form.find('select'), function (key, value) {
                $(value).find('option').prop('selected', false);
                $(value).find('option[value=""]').prop('selected', true);
            });

            $form.find('.js-select2').select2();
        });
    })


  //Mensaje de Cancel Reservation
$(function () {
    $(document).on('click', ".cancel-booking", function (e) {

        e.preventDefault();
        var id = $(this).data('id');
        var reservationsUrl = "/reservations";

        swal({
            title: "CANCEL BOOKING",
            text: "Type <b>CANCEL</b> to cancel this booking",
            type: "input",
            html: true,
            cancelButtonText: 'Close',
            confirmButtonColor: "#ff1a1a",
            showCancelButton: true,
            closeOnConfirm: false,
            closeOnCancel: true,
            //animation: "slide-from-top",
            inputPlaceholder: "CANCEL"
        }, function (inputValue) {
            if (inputValue == "CANCEL") {
                swal({
                    title: "REASONS",
                    text: "Explain the reasons for the cancellation",
                    type: "input",
                    html: true,
                    cancelButtonText: 'Close',
                    confirmButtonColor: "#ff1a1a",
                    showCancelButton: true,
                    closeOnConfirm: false,
                    animation: "slide-from-top",
                    inputPlaceholder: "Reasons",
                    showLoaderOnConfirm: true
                }, function (val) {
                    if (val.length > 4) {
                        $.post(
                            '/reservations/cancel', {id: id, reason: val}
                        ).done(function () {

                            swal("Nice!", "Canceled: " + id, "success");
                           //return false;
                           window.location.replace(reservationsUrl);

                        });
                    } else {
                        swal.showInputError("You need to write something!");
                        return false;
                    }
                });
                return false;
            } else {
                swal.showInputError("You need to write CANCEL!");
                return false;
            }
        });
    });
});

$('body').on('click', '.js-show-booking-search', function () {
    $('.js-hide-booking-search').show();

    $(this).hide();

    $('.js-booking-search').slideDown();
})

$('body').on('click', '.js-hide-booking-search', function () {
    $('.js-show-booking-search').show();

    $(this).hide();

    $('.js-booking-search').slideUp();
})

</script>
@endpush
