<?php
session_start();
require_once '../lib/conn.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Check required fields
$required_fields = ['id', 'plan_name', 'plan_price', 'billing_cycle', 
                   'card_number', 'expiry_date', 'payment_status', 'start_date', 'next_billing_date'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit();
    }
}

// Sanitize input
$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
$plan_name = filter_var($_POST['plan_name'], FILTER_SANITIZE_STRING);
$plan_price = filter_var($_POST['plan_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$billing_cycle = filter_var($_POST['billing_cycle'], FILTER_SANITIZE_STRING);
// $cardholder_name = filter_var($_POST['cardholder_name'], FILTER_SANITIZE_STRING);
$card_number = filter_var($_POST['card_number'], FILTER_SANITIZE_STRING);
$expiry_date = filter_var($_POST['expiry_date'], FILTER_SANITIZE_STRING);
$payment_status = filter_var($_POST['payment_status'], FILTER_SANITIZE_STRING);
// $transaction_id = filter_var($_POST['transaction_id'] ?? '', FILTER_SANITIZE_STRING);
$start_date = filter_var($_POST['start_date'], FILTER_SANITIZE_STRING);
$next_billing_date = filter_var($_POST['next_billing_date'], FILTER_SANITIZE_STRING);

// Validate billing cycle
$allowed_billing_cycles = ['monthly', 'annual'];
if (!in_array($billing_cycle, $allowed_billing_cycles)) {
    echo json_encode(['success' => false, 'message' => 'Invalid billing cycle specified']);
    exit();
}

// Validate payment status
$allowed_payment_statuses = ['active', 'cancelled', 'pending'];
if (!in_array($payment_status, $allowed_payment_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment status specified']);
    exit();
}

// Validate card number (last 4 digits)
if (!preg_match('/^\d{4}$/', $card_number)) {
    echo json_encode(['success' => false, 'message' => 'Invalid card number (last 4 digits required)']);
    exit();
}

// Validate expiry date format (MM/YYYY)
if (!preg_match('/^(0[1-9]|1[0-2])\/\d{4}$/', $expiry_date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid expiry date format (MM/YYYY required)']);
    exit();
}

// Validate dates
if (!DateTime::createFromFormat('Y-m-d', $start_date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid start date format']);
    exit();
}

if (!DateTime::createFromFormat('Y-m-d', $next_billing_date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid next billing date format']);
    exit();
}

// Validate next billing date is after start date
$start_date_obj = new DateTime($start_date);
$next_billing_date_obj = new DateTime($next_billing_date);

if ($next_billing_date_obj <= $start_date_obj) {
    echo json_encode(['success' => false, 'message' => 'Next billing date must be after start date']);
    exit();
}

// Validate plan price
if ($plan_price < 0) {
    echo json_encode(['success' => false, 'message' => 'Plan price cannot be negative']);
    exit();
}

try {
    // Prepare the update statement
    $stmt = $conn->prepare("UPDATE subscriptions SET 
                            plan_name = ?, 
                            plan_price = ?, 
                            billing_cycle = ?, 
                            card_number = ?, 
                            expiry_date = ?, 
                            payment_status = ?, 
                            
                            start_date = ?,
                            next_billing_date = ?
                            WHERE id = ?");
    
    $stmt->bind_param("sdssssssi", 
        $plan_name,
        $plan_price,
        $billing_cycle,
       
        $card_number,
        $expiry_date,
        $payment_status,
      
        $start_date,
        $next_billing_date,
        $id
    );

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Subscription updated successfully',
            'subscription' => [
                'id' => $id,
                'plan_name' => $plan_name,
                'plan_price' => $plan_price,
                'billing_cycle' => $billing_cycle,
                // 'cardholder_name' => $cardholder_name,
                'card_number' => $card_number,
                'expiry_date' => $expiry_date,
                'payment_status' => $payment_status,
                // 'transaction_id' => $transaction_id,
                'start_date' => $start_date,
                'next_billing_date' => $next_billing_date
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made or subscription not found']);
    }

    $stmt->close();
} catch (mysqli_sql_exception $e) {
    error_log("DB error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}

$conn->close();
?>