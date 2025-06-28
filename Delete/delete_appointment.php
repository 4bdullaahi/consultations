<?php
session_start();
require_once '../lib/conn.php';

// Only admin can delete appointments
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Check for appointment ID
if (!isset($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Appointment ID is required']);
    exit();
}

$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

// Delete the appointment
$deleteStmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
$deleteStmt->bind_param("i", $id);
$deleteStmt->execute();

if ($deleteStmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Appointment deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Appointment deletion failed']);
}

$deleteStmt->close();
$conn->close();
?>
