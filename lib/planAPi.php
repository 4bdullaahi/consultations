<?php
// save_subscription.php

// Database connection
require "../lib/conn.php";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die(json_encode(['status' => 'error', 'message' => 'Connection Failed']));
}

// Receive JSON Data
$data = json_decode(file_get_contents("php://input"), true);

// Validate
if (empty($data['customer_name']) || empty($data['plan_name']) || empty($data['price']) || empty($data['card_last4']) || empty($data['subscription_period'])) {
  echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
  exit;
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO subscriptions (customer_name, plan_name, price, card_last4, subscription_period, transaction_date, status) VALUES (?, ?, ?, ?, ?, NOW(), 'Active')");
$stmt->bind_param(
  "ssdss",
  $data['customer_name'],
  $data['plan_name'],
  $data['price'],
  $data['card_last4'],
  $data['subscription_period']
);

if ($stmt->execute()) {
  echo json_encode(['status' => 'success', 'message' => 'Subscription saved successfully']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Database Insert Failed']);
}

$stmt->close();
$conn->close();
?>
