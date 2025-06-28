<?php
session_start();
require_once '../lib/conn.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' ) {
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
$required_fields = ['id', 'patient_id', 'doctor_name', 'appointment_date', 'appointment_time', 
                   'duration', 'status', 'payment_status', 'amount'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit();
    }
}

// Sanitize input
$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
$patient_id = filter_var($_POST['patient_id'], FILTER_SANITIZE_STRING);
$doctor_name = filter_var($_POST['doctor_name'], FILTER_SANITIZE_STRING);
$appointment_date = filter_var($_POST['appointment_date'], FILTER_SANITIZE_STRING);
$appointment_time = filter_var($_POST['appointment_time'], FILTER_SANITIZE_STRING);
$duration = filter_var($_POST['duration'], FILTER_SANITIZE_NUMBER_INT);
$status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);
$payment_status = filter_var($_POST['payment_status'], FILTER_SANITIZE_STRING);
$amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

// Validate status and payment status
$allowed_statuses = ['scheduled', 'completed', 'cancelled'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status specified']);
    exit();
}

$allowed_payment_statuses = ['paid', 'pending', 'failed'];
if (!in_array($payment_status, $allowed_payment_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment status specified']);
    exit();
}

// Validate date and time
if (!DateTime::createFromFormat('Y-m-d', $appointment_date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
    exit();
}

$valid_time = false;
if (DateTime::createFromFormat('H:i:s', $appointment_time)) {
    $valid_time = true;
} elseif (DateTime::createFromFormat('H:i', $appointment_time)) {
    // Optionally, convert to H:i:s
    $appointment_time .= ':00';
    $valid_time = true;
}

if (!$valid_time) {
    echo json_encode(['success' => false, 'message' => 'Invalid time format']);
    exit();
}

// Validate amount
if ($amount < 0) {
    echo json_encode(['success' => false, 'message' => 'Amount cannot be negative']);
    exit();
}

// Validate duration (minimum 15 minutes, increments of 15)
if ($duration < 15 || $duration % 15 !== 0) {
    echo json_encode(['success' => false, 'message' => 'Duration must be in 15-minute increments']);
    exit();
}

try {
    // Prepare the update statement
    $stmt = $conn->prepare("UPDATE appointments SET 
                            patient_id = ?, 
                            doctor_name = ?, 
                            appointment_date = ?, 
                            appointment_time = ?, 
                            duration = ?, 
                            status = ?, 
                            payment_status = ?, 
                            amount = ? 
                            WHERE id = ?");
    
    $stmt->bind_param("ssssissdi", 
        $patient_id,
        $doctor_name,
        $appointment_date,
        $appointment_time,
        $duration,
        $status,
        $payment_status,
        $amount,
        $id
    );

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Appointment updated successfully',
            'appointment' => [
                'id' => $id,
                'patient_id' => $patient_id,
                'doctor_name' => $doctor_name,
                'appointment_date' => $appointment_date,
                'appointment_time' => $appointment_time,
                'duration' => $duration,
                'status' => $status,
                'payment_status' => $payment_status,
                'amount' => $amount
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made or appointment not found']);
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