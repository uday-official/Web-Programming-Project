<?php

@include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    header('location:checkout.php');
    exit;
}

function sendOrderConfirmationEmail($to_email, $customer_name, $order_details, $total_price, $order_id) {
    require 'vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com';
        $mail->Password = 'your-app-password';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('your-email@gmail.com', 'Your Store Name');
        $mail->addAddress($to_email, $customer_name);
        
        $mail->isHTML(true);
        $mail->Subject = 'Order Confirmation - Order #' . $order_id;
        
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4; }
                .header { background-color: #4CAF50; color: white; padding: 30px; text-align: center; border-radius: 5px 5px 0 0; }
                .header h1 { margin: 0; font-size: 28px; }
                .content { background-color: white; padding: 30px; border-radius: 0 0 5px 5px; }
                .order-details { background-color: #f9f9f9; padding: 20px; margin: 20px 0; border-left: 4px solid #4CAF50; }
                .order-details h3 { margin-top: 0; color: #4CAF50; }
                .total { font-size: 20px; font-weight: bold; color: #4CAF50; margin: 15px 0; }
                .button { display: inline-block; padding: 12px 30px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                .success-icon { font-size: 50px; color: #4CAF50; text-align: center; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✓ Order Confirmed!</h1>
                    <p style='margin: 10px 0 0 0;'>Thank you for your purchase</p>
                </div>
                <div class='content'>
                    <h2 style='color: #333;'>Hi {$customer_name},</h2>
                    <p>Great news! Your order has been successfully placed and payment confirmed.</p>
                    
                    <div class='order-details'>
                        <h3>Order Information</h3>
                        <p><strong>Order Number:</strong> #{$order_id}</p>
                        <p><strong>Order Date:</strong> " . date('F d, Y') . "</p>
                        <p><strong>Products:</strong></p>
                        <p>{$order_details}</p>
                        <p class='total'>Total Paid: \${$total_price}</p>
                    </div>
                    
                    <h3>What's Next?</h3>
                    <ul style='line-height: 2;'>
                        <li>Your order is being processed</li>
                        <li>You'll receive a shipping confirmation email once your order ships</li>
                        <li>Estimated delivery: 3-5 business days</li>
                    </ul>
                    
                    <div style='text-align: center;'>
                        <a href='#' class='button'>Track Your Order</a>
                    </div>
                    
                    <p style='margin-top: 30px;'>If you have any questions about your order, please don't hesitate to contact our support team.</p>
                    
                    <p style='margin-top: 20px;'>Thank you for shopping with us!</p>
                    <p><strong>The Team</strong></p>
                </div>
                <div class='footer'>
                    <p>&copy; 2024 Your Electronic Store. All rights reserved.</p>
                    <p>This email was sent to {$to_email}</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mail->AltBody = "Thank you for your order, {$customer_name}! Your order #{$order_id} has been confirmed and payment received. Products: {$order_details}. Total: \${$total_price}. Your order will be delivered in 3-5 business days.";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Error: {$mail->ErrorInfo}");
        return false;
    }
}

try {
    $update_payment = $conn->prepare("UPDATE `orders` SET payment_status = 'completed' WHERE id = ? AND user_id = ?");
    $update_payment->execute([$order_id, $user_id]);

    $order_query = $conn->prepare("SELECT * FROM `orders` WHERE id = ? AND user_id = ?");
    $order_query->execute([$order_id, $user_id]);
    $order = $order_query->fetch(PDO::FETCH_ASSOC);
    
    if ($order) {
        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
        $delete_cart->execute([$user_id]);
        
        $email_sent = sendOrderConfirmationEmail(
            $order['email'],
            $order['name'],
            $order['total_products'],
            $order['total_price'],
            $order_id
        );
        
        unset($_SESSION['pending_order_id']);
        unset($_SESSION['pending_order_email']);
        unset($_SESSION['pending_order_name']);
        unset($_SESSION['pending_order_products']);
        unset($_SESSION['pending_order_total']);
    }
} catch (Exception $e) {
    error_log("Error updating order: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .success-icon {
            font-size: 80px;
            color: #4CAF50;
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease-out;
        }
        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .success-container h1 {
            color: #4CAF50;
            margin-bottom: 10px;
        }
        .success-container p {
            color: #666;
            font-size: 16px;
            margin: 20px 0;
            line-height: 1.6;
        }
        .order-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 30px 0;
            text-align: left;
        }
        .order-info p {
            margin: 10px 0;
            color: #333;
        }
        .order-info strong {
            color: #4CAF50;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        .btn-primary, .btn-secondary {
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        .btn-primary:hover {
            background: #45a049;
        }
        .btn-secondary {
            background: #fff;
            color: #4CAF50;
            border: 2px solid #4CAF50;
        }
        .btn-secondary:hover {
            background: #4CAF50;
            color: white;
        }
        .email-notice {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin: 20px 0;
            text-align: left;
        }
        .email-notice i {
            color: #4CAF50;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="success-container">
    <div class="success-icon">
        <i class="fas fa-check-circle"></i>
    </div>
    
    <h1>Payment Successful!</h1>
    <p>Thank you for your order. Your payment has been processed successfully.</p>
    
    <?php if ($order): ?>
    <div class="order-info">
        <h3 style="margin-top: 0; color: #4CAF50;">Order Details</h3>
        <p><strong>Order ID:</strong> #<?= $order_id; ?></p>
        <p><strong>Order Date:</strong> <?= $order['placed_on']; ?></p>
        <p><strong>Total Amount:</strong> $<?= number_format($order['total_price'], 2); ?></p>
        <p><strong>Payment Status:</strong> <span style="color: #4CAF50;">✓ Paid</span></p>
        <p><strong>Delivery Address:</strong> <?= $order['address']; ?></p>
    </div>
    <?php endif; ?>
    
    <div class="email-notice">
        <i class="fas fa-envelope"></i>
        <strong>Confirmation Email Sent!</strong>
        <p style="margin: 5px 0 0 0;">We've sent an order confirmation to <strong><?= $order['email'] ?? ''; ?></strong></p>
        <p style="margin: 5px 0 0 0; font-size: 14px;">Please check your inbox (and spam folder) for order details.</p>
    </div>
    
    <p>Your order is being processed and will be delivered soon. We'll send you a shipping confirmation email once your order is on its way.</p>
    
    <div class="action-buttons">
        <a href="orders.php" class="btn-primary">
            <i class="fas fa-box"></i>
            View My Orders
        </a>
        <a href="shop.php" class="btn-secondary">
            <i class="fas fa-shopping-bag"></i>
            Continue Shopping
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>