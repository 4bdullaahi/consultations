<?php
// filepath: c:\xampp\htdocs\healthcare\saves\save_subscription.php
session_start();
require_once("../lib/conn.php");

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Collect and sanitize inputs
$plan_name = trim($_POST['plan_name'] ?? '');
$plan_price = trim($_POST['plan_price'] ?? '');
$billing_cycle = trim($_POST['billing_cycle'] ?? '');
$cardholder_name = trim($_POST['cardholder_name'] ?? '');
$card_number = trim($_POST['card_number'] ?? '');
$expiry_date = trim($_POST['expiry_date'] ?? '');
$transaction_id = trim($_POST['transaction_id'] ?? '');
$next_billing_date = trim($_POST['next_billing_date'] ?? '');

if (!$plan_name || !$plan_price || !$billing_cycle || !$cardholder_name || !$card_number || !$expiry_date || !$transaction_id || !$next_billing_date) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Only store last 4 digits of card
$card_number = substr(preg_replace('/\D/', '', $card_number), -4);

// Insert into subscriptions table
$stmt = $conn->prepare("INSERT INTO subscriptions 
    (user_id, plan_name, plan_price, billing_cycle, cardholder_name, card_number, expiry_date, transaction_id, next_billing_date) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param(
    "isdssssss",
    $user_id,
    $plan_name,
    $plan_price,
    $billing_cycle,
    $cardholder_name,
    $card_number,
    $expiry_date,
    $transaction_id,
    $next_billing_date
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Subscription saved']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}
$stmt->close();
$conn->close();