<?php

use Stripe\Stripe;

if (!isset($_GET['amount'])) {
    throw new \RuntimeException('Provide a query parameter "amount" for amount to be paid (in cents)');
}

if (!isset($_GET['order_id'])) {
    throw new \RuntimeException('Provide a query parameter "order_id"');
}

require __DIR__ . '/../../vendor/autoload.php';

if (isset($_POST['stripeToken'])) {
    // Set your secret key: remember to change this to your live secret key in production
    // See your keys here: https://dashboard.stripe.com/account/apikeys
    Stripe::setApiKey(getenv('SECRET_STRIPE_API_KEY'));

    // Token is created using Stripe.js or Checkout!
    // Get the payment token submitted by the form:
    $token = $_POST['stripeToken'];

    // Charge the user's card:
    try {
        $charge = \Stripe\Charge::create(array(
            'amount' => (int)$_GET['amount'],
            'currency' => 'eur',
            'description' => 'Example charge',
            'source' => $token,
            'metadata' => ['order_id' => (string)$_GET['order_id']],
        ));

        header('Content-Type: text/plain', true, 200);
        echo 'Payment successful';
        exit;
    } catch (\Exception $exception) {
        header('Content-Type: text/plain', true, 500);
        echo "Payment unsuccesful\n\n";
        echo $exception;
        exit;
    }
}

?>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <style type="text/css">
        .StripeElement {
            background-color: white;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid transparent;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }

        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }

        .StripeElement--invalid {
            border-color: #fa755a;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <h1>External Payment Provider</h1>
            <form action="#" method="post" id="payment-form" class="form">
                <div class="alert alert-info">You're about to pay <strong>&euro;<?php
                        echo number_format($_GET['amount']/100, 2, ',', '.');
                        ?></strong></div>
                <div class="form-form-group">
                    <label for="card-element">
                        Credit or debit card
                    </label>
                    <div id="card-element">
                        <!-- a Stripe Element will be inserted here. -->
                    </div>

                    <!-- Used to display Element errors -->
                    <div id="card-errors" class="help-block"></div>
                    <div class="help-block">
                        Use card number <code>4242424242424242</code>,<br>
                        any future expiry date, e.g. <code>1220</code>,<br>
                        a random 3-digit CCV number, e.g. <code>123</code>,<br>
                        and a random 5-digit postal code, e.g. <code>12345</code>.
                    </div>
                </div>

                <button class="btn btn-primary">Submit Payment</button>
            </form>
        </div>
    </div>
</div>
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('<?php echo getenv('PUBLISHABLE_STRIPE_API_KEY'); ?>');
    var elements = stripe.elements();

    // Custom styling can be passed to options when creating an Element.
    var style = {
        base: {
            // Add your base input styles here. For example:
            fontSize: '16px',
            lineHeight: '24px'
        }
    };

    // Create an instance of the card Element
    var card = elements.create('card', {style: style});

    // Add an instance of the card Element into the `card-element` <div>
    card.mount('#card-element');

    card.addEventListener('change', function (event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    function stripeTokenHandler(token) {
        // Insert the token ID into the form so it gets submitted to the server
        var form = document.getElementById('payment-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        form.appendChild(hiddenInput);

        // Submit the form
        form.submit();
    }

    // Create a token or display an error the form is submitted.
    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        stripe.createToken(card).then(function (result) {
            if (result.error) {
                // Inform the user if there was an error
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                // Send the token to your server
                stripeTokenHandler(result.token);
            }
        });
    });
</script>
</body>
</html>
