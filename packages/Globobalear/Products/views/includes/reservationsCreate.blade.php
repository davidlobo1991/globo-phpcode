<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-ticket"></i> Pass Selection</h4>
    </div>
    <div class="panel-body">

        <div class="col-md-6">

             
              <div class="form-group{{ $errors->has('products') || $errors->has('products') ? ' has-error' : '' }} showSelector">
                {!! Form::label('products', 'Products')!!}
                {!! Form::select('products', [], null, ['id' => 'products', 'class' => 'form-control','v-validate' => "'required'"]) !!}

                <div v-if="errors.has('products')" class="help-inline text-danger">@{{ errors.first('products') }}</div>
                
                </div>
            </div>
       

        <div class="col-md-6">
           
            <div class="form-group{{ $errors->has('passes') || $errors->has('passes') ? ' has-error' : '' }} passSelector">
                 {!! Form::label('passes', 'Passes')!!}
                {!! Form::select('passes', [], null, ['id' => 'passes', 'class' => 'form-control']) !!}

                <div v-if="errors.has('passes')" class="help-inline text-danger">@{{ errors.first('pass_id') }}</div>
            </div>
       </div>

        <div class="col-md-12" id="tableTickets">

        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
      let $passes = $('#passes');
      let $products = $('#products');

      $passes.select2();

      $products.select2({
        ajax: {
          url: '/list/products',
          dataType: 'json',
          method: 'POST',
          delay: 250,

          data: function (params) {
            return {
              q: params.term, // search term
            }
          },
          processResults: function (data) {
            return {
              results: data
            }
          }
        },
        templateResult: returnData,
        templateSelection: returnData,
      });

      $(document).ready(function () {

        $products.change(function () {
            $.ajax({
                url: '/data/checkPasses',
                data: {product_id: $(this).val()},
                method: 'GET',
            }).done(function (params) {
                if (params.status === false) {
                    $passes.attr('disabled', true);
/*
                    $passes.attr('v-validate', 'required');
*/
                    $('#tableTickets').html(params.view);
                }
            }).fail(function (e) {
                    console.log(e);
            });

          $passes.removeAttr('disabled');
          $passes.removeAttr('v-validate');

            $passes.select2({
            ajax: {
              url: '/list/passes',
              dataType: 'json',
              method: 'POST',
              delay: 250,

              data: function (params) {
                return {
                  q: $('#products').val(), // search term
                  s: params.term
                }
              },

              processResults: function (data) {
                return {
                  results: data
                }
              }
            },
            templateResult: returnDataPass,
            templateSelection: returnDataPass,
          })
        });

        $passes.change(function () {
          $.post('/table/tickets', {pass: $passes.val()})
            .done(function (data) {
              $('#tableTickets').html(data)
            })
        })

      });

      function returnData (data) {
        return data.name
      }

      function returnDataPass (data) {
        return data.formattedDate
      }
    </script>
@endpush
