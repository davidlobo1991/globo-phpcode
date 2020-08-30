<div class="panel panel-default">
    <div class="panel-heading">
        <h4><i class="fa fa-user"></i> Customer Info</h4>
    </div>
    <div class="panel-body">
        <div class="form-group col-md-12">

            {!! Form::checkbox('recurrent', 1, null, ['class' => 'iCheck', 'id' => 'recurring', 'placeholder' => 'Recurring Customer']) !!}
            Recurring customer
        </div>

        <div class="hidden col-md-12" id="showRecurring">
            <div class="form-group">
                {!! Form::select(null, [], null, ['id' => 'customers', 'class' => 'form-control']) !!}
            </div>
        </div>

        <div class="form-group{{ $errors->has('name') || $errors->has('name') ? ' has-error' : '' }} col-md-6">
            <float-label>

                {!! Form::hidden('customer_id', $reservation->customer_id ?? null, ['class' => 'form-control']) !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Name', 'v-validate' => "'required'",'required' => 'required','id' => 'name']) !!}
            </float-label>
            <div v-if="errors.has('name')" class="help-inline text-danger">@{{ errors.first('name') }}</div>
        </div>

        <div class="form-group{{ $errors->has('surname') || $errors->has('surname') ? ' has-error' : '' }} col-md-6">
            <float-label>

                {!! Form::hidden('customer_id', $reservation->customer_id ?? null, ['class' => 'form-control']) !!}
                {!! Form::text('surname', null, ['class' => 'form-control', 'placeholder' => 'Surname', 'v-validate' => "'required'",'required' => 'required','id' => 'surname']) !!}
            </float-label>
            <div v-if="errors.has('surname')" class="help-inline text-danger">@{{ errors.first('surname') }}</div>
        </div>

        <div class="form-group{{ $errors->has('email') || $errors->has('email') ? ' has-error' : '' }} col-md-6">
            <float-label>
                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Email', 'v-validate' => "'required'",'required' => 'required','id' => 'email']) !!}
            </float-label>
            <div v-if="errors.has('email')" class="help-inline text-danger">@{{ errors.first('email') }}</div>
        </div>

        <div class="form-group{{ $errors->has('phone') || $errors->has('phone') ? ' has-error' : '' }} col-md-6">
            <float-label>
                {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Phone','required' => 'required','id' => 'phone']) !!}
            </float-label>

        </div>

        <div class="form-group{{ $errors->has('identification_number') || $errors->has('identification_number') ? ' has-error' : '' }} col-md-6">
            <float-label>
                {!! Form::text('identification_number', null, ['class' => 'form-control', 'placeholder' => 'Personal identification number','required' => 'required','id' => 'identification_number']) !!}
            </float-label>
        </div>
    </div>
</div>

@push('scripts')
<script>
  $('#customers').select2({
    ajax: {
      url: '/list/customers',
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
          results: data.items
        }
      }
    },
    minimumInputLength: 3,
    templateResult: returnData,
    templateSelection: returnData,
  })

  $(document).ready(function () {
    $('#recurring').on('ifChanged', function () {
      if ($('#recurring:checked').length) {
        $('#showRecurring').removeClass('hidden')
        $('.select2').css('width', '100%')

      } else {
        $('#showRecurring').addClass('hidden')
        $('#customers').empty();


        $('input[name="customer_id"]').val("");
        $('input[name="name"]').val("").attr('readonly', false);
        $('input[name="surname"]').val("").attr('readonly', false);
        $('input[name="phone"]').val("").attr('readonly', false);
        $('input[name="email"]').val("").attr('readonly', false);
        $('input[name="identification_number"]').val("").attr('readonly', false);
      }
    })

    $('#customers').change(function () {
      let id = $(this).val()

      $.post('/get/customers', {id: id})
        .done(function (customer) {
            $('input[name="customer_id"]').val(customer.id);
            $('input[name="name"]').val(customer.name).attr('readonly', true);
            $('input[name="surname"]').val(customer.surname).attr('readonly', true);
            $('input[name="phone"]').val(customer.phone).attr('readonly', true);
            $('input[name="email"]').val(customer.email).attr('readonly', true);
            $('input[name="identification_number"]').val(customer.identification_number).attr('readonly', true);

        })
    })
  })

  function returnData (data) {
    return data.name + ' ' + data.surname + ' | ' + data.email
  }
</script>
@endpush
