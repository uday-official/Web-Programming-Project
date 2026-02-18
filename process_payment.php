<?php

@include 'config.php';

session_start();

// Stripe Secret Key
define('STRIPE_SECRET_KEY', 'sk_test_your_stripe_secret_key_here');

header('Content-Type: application/json');

try {
    // Get POST data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $amount = $data['amount'] ?? 0;
    $order_id = $data['order_id'] ?? 0;
    
    if ($amount <= 0 || $order_id <= 0) {
        throw new Exception('Invalid payment amount or order ID');
    }
    
    // Initialize Stripe
    require_once('vendor/autoload.php');
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
    
    // Create Payment Intent
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount, // Amount in cents
        'currency' => 'usd',
        'description' => 'Order #' . $order_id,
        'metadata' => [
            'order_id' => $order_id
        ]
    ]);
    
    echo json_encode([
        'clientSecret' => $paymentIntent->client_secret
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}

?>