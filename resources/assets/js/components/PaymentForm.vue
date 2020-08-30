<template>
    <div class="panel-body" id="payments">
        <div class="box">
           
        <div class="row text-center status">
            <div class="col-md-6 bg-green" style="padding-top: 10px;">
                <p><b>TOTAL:</b> <span id="total">{{ fixedTotal }}</span>€</p>
            </div>
            <div class="col-md-6 bg-red" style="padding-top: 10px;">
                <p><b>REMAINING:</b> <span id="remaining">{{ fixedPending }}</span>€</p>
            </div>
        </div>

        <hr>

        <div class="form-group col-md-6" v-for="payment in JSON.parse(payments)">
            <float-label>
                <input :name="'payment[' + payment.id + '][total]'" value="0" @change="refreshPending"
                       class="form-control paymentInput"
                       :placeholder="payment.name">
            </float-label>
        </div>

        <template v-if="reservation.payment_methods.length">

            <div class="pull-right col-md-3">
            <span class="btn btn-block btn-info" data-toggle="collapse" data-target="#paid">
                Payments
            </span>
            </div>

            <div class="col-md-12">
                <div id="paid" class="collapse">
                    <hr>

                    <ul class="list-group">
                        <li class="list-group-item" v-for="paid in reservation.payment_methods">
                            <b>{{ paid.name }}</b>: <i>{{ paid.pivot.total }}€</i> 
                            <div style="padding: 0px 10px;" @click="removePayment" class="btn btn-danger pull-right clearfix">
                                <i class="fa fa-trash"></i>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </template>

    </div>
</template>

<script>

  export default {
    data: function () {
      return {
        pending: this.reservation.pendingToPay
      }
    },

    props: [
      'reservation',
      'payments',
    ],

    methods: {
      refreshPending: function () {
        this.pending = this.reservation.pendingToPay
        let that = this

        $('.paymentInput').each(function () {
          let input = parseFloat($(this).val()).toFixed(2)

          if (!isNaN(input))
            if (that.pending >= input) {
              that.pending -= input
            }
            else {
              swal({
                title: 'Oops...',
                html: 'The total cost is <b>' + that.reservation.totalPrice + '</b> euros',
                type: 'error'
              })
              $(this).val(0)
            }

          if (that.pending) {
            $('#savePayments').addClass('disabled')
            $('#savePayments').attr('disabled')
          } else {
            $('#savePayments').removeClass('disabled')
            $('#savePayments').removeAttr('disabled')
          }
        })
      },
      removePayment: function () {

      }
    },

    computed: {
      fixedPending: function () {
        return this.pending.toFixed(2)
      },
      fixedTotal: function () {
        return this.reservation.totalPrice.toFixed(2)
      }
    }
  }

</script>