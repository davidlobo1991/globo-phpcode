<script>

    // Helper functions

    function getElements(el) {
        return Array.prototype.slice.call(document.querySelectorAll(el));
    }

    function hideElement(el) {
        document.body.querySelector(el).style.display = 'none';
    }

    function showElement(el) {
        document.body.querySelector(el).style.display = 'block';
    }

    // Listen for changes to the radio fields
    getElements('input[name=payment-option]').forEach(function(el) {
        el.addEventListener('change', function(event) {

            // If PayPal is selected, show the PayPal button

            if (event.target.value === 'paypal') {
                hideElement('#card-button-container');
                showElement('#paypal-button-container');
            }

            // If Card is selected, show the standard continue button

            if (event.target.value === 'card') {
                showElement('#card-button-container');
                hideElement('#paypal-button-container');
            }
        });
    });

    hideElement('#card-button-container');

    // Render the PayPal button
    paypal.Button.render({

        env: 'sandbox',

        client: {
            sandbox:    'AZDxjDScFpQtjWTOUtWKbyN_bDt4OgqaF4eYXlewfBP4-8aqX3PiV8e1GWU6liB2CUXlkA59kJXE7M6R',
            production: '<insert production client id>'
        },

        // Specify the style of the button
        style: {
            label: 'paypal',
            size:  'responsive',    // small | medium | large | responsive
            shape: 'rect',     // pill | rect
            color: 'blue',     // gold | blue | silver | black
            tagline: false
        },

        // Show the buyer a 'Pay Now' button in the checkout flow
        commit: true,

        payment: function(data, actions) {
            return actions.payment.create({
                payment: {
                    transactions: [
                        {
                            amount: { total: '0.01', currency: 'USD' }
                        }
                    ]
                }
            });
        },

        // payment() is called when the button is clicked
        payment: function() {

            // Set up a url on your server to create the payment
            var CREATE_URL = '/web/payment/create';

            // Make a call to your server to set up the payment
            return paypal.request.post(CREATE_URL)
                .then(function(res) {
                    //return res.paymentID;
                    return res.id;
                });
        },

        // onAuthorize() is called when the buyer approves the payment
        onAuthorize: function(data, actions) {

            // Set up a url on your server to execute the payment
            var EXECUTE_URL = '/web/payment/execute';

            // Set up the data you need to pass to your server
            var data = {
                paymentID: data.paymentID,
                payerID: data.payerID
            };

            // Make a call to your server to execute the payment
            return paypal.request.post(EXECUTE_URL, data)
                .then(function (res) {
                    $.redirect('/', {'arg1': 'value1', 'arg2': 'value2'});
                    // var url = '/';
                    // var form = $('<form action="' + url + '" method="post">' +
                    //     '<input type="text" name="api_url" value="' + Return_URL + '" />' +
                    //     '</form>');
                    // $('body').append(form);
                    // form.submit();
                });
        }

    }, '#paypal-button-container');

</script>