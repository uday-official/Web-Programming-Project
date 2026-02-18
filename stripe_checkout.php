<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

define('STRIPE_SECRET_KEY', 'sk_test_your_stripe_secret_key_here');
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_your_stripe_publishable_key_here');

$order_id = $_SESSION['pending_order_id'] ?? null;
$order_total = $_SESSION['pending_order_total'] ?? 0;
$order_name = $_SESSION['pending_order_name'] ?? '';

if (!$order_id || $order_total == 0) {
    header('location:checkout.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        .payment-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .payment-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .payment-header h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .order-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .order-summary p {
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            padding-top: 10px;
            border-top: 2px solid #ddd;
        }
        #card-element {
            border: 1px solid #ccc;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        #card-errors {
            color: #e74c3c;
            margin-top: 10px;
        }
        .pay-button {
            width: 100%;
            padding: 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .pay-button:hover {
            background: #45a049;
        }
        .pay-button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .loading {
            display: none;
            text-align: center;
            margin-top: 10px;
        }
        .loading.active {
            display: block;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="payment-container">
    <div class="payment-header">
        <h2><i class="fas fa-lock"></i> Secure Payment</h2>
        <p>Complete your purchase securely with Stripe</p>
    </div>

    <div class="order-summary">
        <h3>Order Summary</h3>
        <p>
            <span>Order ID:</span>
            <span>#<?= $order_id; ?></span>
        </p>
        <p>
            <span>Customer:</span>
            <span><?= $order_name; ?></span>
        </p>
        <p class="total-amount">
            <span>Total Amount:</span>
            <span>$<?= number_format($order_total, 2); ?></span>
        </p>
    </div>

    <form id="payment-form">
        <div id="card-element"></div>
        <div id="card-errors" role="alert"></div>
        
        <button type="submit" id="submit-button" class="pay-button">
            <i class="fas fa-credit-card"></i> Pay $<?= number_format($order_total, 2); ?>
        </button>
        
        <div class="loading" id="loading">
            <i class="fas fa-spinner fa-spin"></i> Processing payment...
        </div>
    </form>
</div>

<script>
    const stripe = Stripe('<?= STRIPE_PUBLISHABLE_KEY; ?>');
    const elements = stripe.elements();
    
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#e74c3c'
            }
        }
    });
    
    cardElement.mount('#card-element');
    
    cardElement.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
    
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const loading = document.getElementById('loading');
    
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        submitButton.disabled = true;
        loading.classList.add('active');
        
        try {
            const response = await fetch('process_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    amount: <?= $order_total * 100; ?>,
                    order_id: <?= $order_id; ?>
                })
            });
            
            const data = await response.json();
            
            if (data.error) {
                throw new Error(data.error);
            }
            
            const {error, paymentIntent} = await stripe.confirmCardPayment(
                data.clientSecret,
                {
                    payment_method: {
                        card: cardElement
                    }
                }
            );
            
            if (error) {
                document.getElementById('card-errors').textContent = error.message;
                submitButton.disabled = false;
                loading.classList.remove('active');
            } else if (paymentIntent.status === 'succeeded') {
                window.location.href = 'payment_success.php?order_id=<?= $order_id; ?>';
            }
        } catch (error) {
            document.getElementById('card-errors').textContent = error.message;
            submitButton.disabled = false;
            loading.classList.remove('active');
        }
    });
</script>

<?php include 'footer.php'; ?>

</body>
</html>