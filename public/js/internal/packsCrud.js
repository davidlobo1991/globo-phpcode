$('.input-daterange').datepicker({
    keyboardNavigation: false,
    forceParse: false,
    format: "dd-mm-yyyy",
    todayHighlight: true,
    clearBtn: true,
    todayBtn: "linked",
});

$('#addLine').click(function(e) {
    e.preventDefault();
    $.post(GENERATELIST, {element: qty++})
        .done(function(data) {
            $('#lines').append(data.view);
        });
})

$('#addLineShows').click(function(e) {
    e.preventDefault();
    $.post(GENERATE_LIST_SHOWS_PACK, {element: qtyShows++})
        .done(function(data) {
            $('#linesshows').append(data.view);
        });
})

$('body').on('ifChanged', '.js-on-disable', function (e) {
    $($(this).data('on-disable-target')).prop('disabled', !$(this).is(':checked')).iCheck('uncheck').iCheck('update');
})

$('body').on('click', '.deleteLine', function (e) {
    e.preventDefault()
    $(this).parent().parent().parent().remove()
})

$('body').on('change', '.js-show-select', function () {
    const productId = $(this).val();
    const $seatTypesContainer = $(this).parents('.js-show-container').find('.js-seat-types');
    const num = $(this).data('num');
    UpdateSeatype(productId, $seatTypesContainer, num);
})

$('body').on('change', '.js-show-pirates-select', function () {
    const showId = $(this).val();
    const $seatTypesContainer = $(this).parents('.js-show-pirates-container').find('.js-show-seat-types');
    const num = $(this).data('num');
    UpdateShowSeatype(showId, $seatTypesContainer, num);
});

$('body').on('ifChecked', '.js-product-ticket-type', function () {
    console.log('hola');
})

// listado de seatype acorde al producto seleccionado
function UpdateSeatype(productId, $seatTypesContainer, num){
    $.ajax({
        type: "POST",
        url: LIST_SEAT_TYPES,
        data: {
            productId: productId,
            element: num
        },
        success: function(data) {
            $seatTypesContainer.html(data.html);

            $seatTypesContainer.find('.iCheck').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
        }
    });
}

// listado de seatype acorde al producto seleccionado
function UpdateShowSeatype(showId, $seatTypesContainer, num){
    $.ajax({
        type: "POST",
        url: LIST_SHOW_SEAT_TYPES,
        data: {
            showId: showId,
            element: num
        },
        success: function(data) {
            $seatTypesContainer.html(data.html);

            $seatTypesContainer.find('.iCheck').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%'
            });
        }
    });
}
