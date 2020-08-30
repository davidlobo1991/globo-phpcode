<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-user"></i> Wristband</h4>
    </div>
    <div class="panel-body">


        <div class="col-md-12 hidden">
            <button class="btn btn-primary hidden" id="addLine">Add Line</button>

            {{--<hr>--}}
        </div>

        <div class="col-md-12" id="lines">
        </div>

    </div>
</div>

@push('scripts')
    <script type="text/javascript">
        let qtyString = '{{ count($reservation->reservationWristbandPasses) or 0 }}';
        let qty = 0; //parseInt(qtyString);

        $(document).ready(function () {

            setTimeout(function(){
                $('#addLine').trigger('click')
            }, 300);

            setTimeout(function(){
                $('#app').find('.js-show-subselect').trigger('change')
            }, 700);

        })

        $('#addLine').click(function (e) {
            e.preventDefault()
            if(qty > 5){
                return false;
            }

            $.post('/wristbands/form-select-component', {
                element: qty,
                reservation: '{{ $reservation->id }}'
            }).done(function (data) {
                $('#lines').append(data)
                addLine(qty);
            })
        })

        $('body').on('click', '.deleteLine', function (e) {
            e.preventDefault()
            $(this).parent().parent().remove()
        })

        function addLine(num) {
            $('body').on('change', '.js-show-select-' + num, function () {

                var q = $(this).val()
                var subselect = $(this).parents('.js-show-container').find('.js-show-subselect')

                $(subselect).removeAttr("disabled")
                $(subselect).find('option').remove()
                setOptionsToWristbandPasses(q, subselect)
            });

            qty++
        }

        function removeDiabledQuantity($selector) {
            //$('body').on('change', $($selector), function () {
            var quantity = $($selector).parents('.js-show-container').find('.quantity');
            $(quantity).val(1);
            $(quantity).removeAttr("disabled");
            //})
        }

        function addDiabledQuantity($selector) {
            //$('body').on('change', $($selector), function () {
            var quantity = $($selector).parents('.js-show-container').find('.quantity');
            $(quantity).val(1);
            $(quantity).attr("disabled", true);
            //})
        }

        function setOptionsToWristbandPasses(q, $selector){
            $.ajax({
                url: '/list/wristband-passes',
                dataType: 'json',
                method: 'POST',
                delay: 250,
                data: {q: q},

                success: function(data) {
                    wristbandPasses = data;
                    wristbandPasses.forEach(function(item){
                        if(item != 0){
                            $('<option>').val(item.id).text(item.title).appendTo($selector);
                        }
                    });

                    $($selector).select2({
                        placeholder: 'Wristbands passes not found...'
                    })

                    if($selector.val() > 0){
                        removeDiabledQuantity($selector)
                    }else{
                        addDiabledQuantity($selector)
                    }

                }
            });
        }

        function getProductsList($pass) {
            let $pass_id = $pass.val()
            console.log('getPr: /wristband-pass/' + $pass_id + '/products-html')
            $.post('/wristband-pass/' + $pass_id + '/products-html', {
                pass_id: $pass_id
            }).done(function (data) {
                $pass.closest('div.js-show-container').find('div.products-list').remove()
                $pass.closest('div.js-show-container').append(data)
            })
        }

        $('#app').on('change', '.js-show-subselect', function () {
            getProductsList($(this));
        });

    </script>
@endpush