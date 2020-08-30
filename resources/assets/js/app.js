/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');

window.Vue = require('vue');

import VeeValidate from 'vee-validate'
import VueFloatLabel from 'vue-float-label'

Vue.use(VeeValidate);
Vue.use(VueFloatLabel);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('generate-passes', require('./components/GeneratePasses.vue'))

const app = new Vue({
    el: '#app',

    data: () => ({
        showPassword: true,
        recurringCustomer: false,
    }),

    methods: {
        validateBeforeSubmit(e) {
            this.$validator.validateAll().then(() => {
                document.getElementById(e.target.id).submit();
            });
        },
    }
});

$('input.iCheck').iCheck({
  checkboxClass: 'icheckbox_square-blue',
  radioClass: 'iradio_square-blue',
  increaseArea: '20%'
});

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

$(document).ready(function () {
  $('body').on('click', '#remove', function (e) {
    e.preventDefault()
    let $form = $(this).parent()

    swal({
      title: 'Are you sure?',
      text: 'You won\'t be able to revert this!',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, continue!'
    }).then(function () {
      $form.unbind().submit()
    })
  })
})