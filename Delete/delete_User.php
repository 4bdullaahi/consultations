<?php
session_start();
require_once '../lib/conn.php';

// Hubi haddii user-ka uu yahay admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Hubi in request-ka uu yahay POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Hubi in ID la helay
if (!isset($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit();
}

$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);

// Hel user-ka si aad u ogaato haddii uu leeyahay sawir
$getUserStmt = $conn->prepare("SELECT img FROM users WHERE id = ?");
$getUserStmt->bind_param("i", $id);
$getUserStmt->execute();
$result = $getUserStmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

$user = $result->fetch_assoc();
$imagePath = $user['img']; // Tusaale: uploads/user_abc123.jpg

// Tirtir user-ka database-ka
$deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$deleteStmt->bind_param("i", $id);
$deleteStmt->execute();

if ($deleteStmt->affected_rows > 0) {
    // Haddii user la tirtiro, tirtir image haddii uu jiro
    if ($imagePath && file_exists('../' . $imagePath)) {
        unlink('../' . $imagePath); // Tusaale: ../uploads/filename.jpg
    }

    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'User deletion failed']);
}

// Xir statement-yada
$getUserStmt->close();
$deleteStmt->close();
$conn->close();
?>
