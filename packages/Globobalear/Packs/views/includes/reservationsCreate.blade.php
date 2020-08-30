<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-ticket"></i> Pack Selection</h4>
    </div>
    <div class="panel-body">

        <div class="col-md-6">

             
              <div class="form-group{{ $errors->has('pack') || $errors->has('pack') ? ' has-error' : '' }} showSelector">
                {!! Form::label('pack', 'Packs')!!}
                  {!! Form::select('pack', $pack, null, ['class' => 'form-control select','v-validate' => "'required'", 'id' => 'pack']) !!}
               
                <div v-if="errors.has('pack')" class="help-inline text-danger">@{{ errors.first('pack') }}</div>
                
                </div>
            </div>
       

        <div class="col-md-6">
           
        {!! Form::label('quantity', 'Quantity')!!}
        {!! Form::number("quantity",1, ['class' => 'form-control','min' => '1','step' => "any", 'id' => 'quantity', 'required'=> true]) !!}
                               
       </div>

        <div class="col-md-12" id="tablePacks">

        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript">
      $('select.select').select2({
        placeholder: 'Select a:'
      })

      let $pack = $('#pack')
      

      $(document).ready(function () {

        $pack.change(function () {
          $.post('/table/packs', {pack: $pack.val()})
            .done(function (data) {
              $('#tablePacks').html(data)
            })
        })

      })

      function returnData (data) {
        return data.name
      }

      function returnDataPass (data) {
        return data.formattedDate
      }
    </script>
@endpush
