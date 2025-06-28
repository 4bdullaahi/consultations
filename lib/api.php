<?php
// Database Connection
require 'conn.php';

// Set content type
header('Content-Type: application/json');

// Read action from request
$action = $_GET['action'] ?? '';

if ($action == 'get_doctors') { // Fixed action name
    // Fetch all doctors
    $stmt = $conn->prepare("SELECT id, username, sepcialization, price, img FROM doctors"); // Include price
    $stmt->execute();
    $result = $stmt->get_result();

    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "doctors" => $doctors
    ]);

} elseif ($action == 'book_appointment') {
    // Book an appointment
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    $requiredFields = ['doctor', 'date', 'time', 'cardName', 'cardNumber', 'expiry', 'cvc'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing field: $field"]);
            exit;
        }
    }

    // Fetch doctor_id, doctor_name, and price based on doctor name
    $stmt = $conn->prepare("SELECT id, username,  sepcialization,price FROM doctors WHERE username = ?");
    $stmt->bind_param("s", $data['doctor']);
    $stmt->execute();
    $result = $stmt->get_result();
    $doctor = $result->fetch_assoc();

    if (!$doctor) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid doctor selected!"]);
        exit;
    }

    // You need to get the patient_id from session or request
    session_start();
    $patient_id = $_SESSION['user_id'] ?? null; // Or however you store the logged-in user

    if (!$patient_id) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "User not authenticated. Please log in."]);
        exit;
    }

    $doctor_id = $doctor['id'];
    $doctor_name = $doctor['username'];
    $appointment_date = $data['date'];
    $appointment_time = $data['time'];
    $amount = $doctor['price'];
    $payment_method = 'credit_card'; // or get from $data if you support more
    $card_last_four = substr($data['cardNumber'], -4);
    $created_at = date('Y-m-d H:i:s');
    $updated_at = $created_at;

    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO appointments 
        (patient_id, doctor_id, doctor_name, appointment_date, appointment_time, amount, payment_method, card_last_four, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "iisssdssss",
        $patient_id,
        $doctor_id,
        $doctor_name,
        $appointment_date,
        $appointment_time,
        $amount,
        $payment_method,
        $card_last_four,
        $created_at,
        $updated_at
    );

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "data" => [
                "doctor_name" => $doctor_name,
                "appointment_date" => $appointment_date,
                "appointment_time" => $appointment_time,
                "amount" => $amount
            ]
        ]);
        exit;
    } else {
        error_log("SQL Error: " . $stmt->error);
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to book appointment!", "error" => $stmt->error]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid action!"]);
}
?>
