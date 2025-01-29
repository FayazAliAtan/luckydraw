<?php
require('vendor/autoload.php');
use Razorpay\Api\Api;

// Database Connection
$host = "localhost";
$dbname = "luckydraw";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password

$mysqli = new mysqli($host, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

// Razorpay API Credentials
$apiKey = 'YOUR_API_KEY';
$apiSecret = 'YOUR_API_SECRET';
$api = new Api($apiKey, $apiSecret);

// Handle Payment Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $phone = htmlspecialchars($_POST['phone']);
    $upi = htmlspecialchars($_POST['upi']);
    $amount = 100 * 100; // ₹100 in paise

    try {
        // Create Razorpay Order
        $order = $api->order->create([
            'receipt'         => 'order_rcptid_' . time(),
            'amount'          => $amount,
            'currency'        => 'INR',
            'payment_capture' => 1 // Auto-capture
        ]);

        $orderId = $order['id'];

        // Save Order Details in Database
        $stmt = $mysqli->prepare("INSERT INTO transactions (name, phone, upi, order_id, amount, status) VALUES (?, ?, ?, ?, ?, ?)");
        $status = 'created';
        $stmt->bind_param("ssssis", $name, $phone, $upi, $orderId, $amount, $status);

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'orderId' => $orderId,
                'amount' => $amount / 100
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $stmt->error
            ]);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

$mysqli->close();
?>
