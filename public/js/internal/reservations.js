let $passes = $('#passes')
let $products = $('#products')

$passes.select2()
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
})

$(document).ready(function () {
  $products.change(function () {
    $passes.removeAttr('disabled')
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
  })
})

function returnData (data) {
  return data.name
}

function returnDataPass (data) {
  return data.formattedDate
}