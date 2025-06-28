<?php

session_start();
header('Content-Type: application/json');
require_once("../lib/conn.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Collect and sanitize input
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$img = null;

// Handle image upload if provided
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $imgTmpPath = $_FILES['photo']['tmp_name'];
    $imgName = time() . '_' . basename($_FILES['photo']['name']);
    $uploadDir = '../uploads/';
    $imgUploadPath = $uploadDir . $imgName;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($imgTmpPath, $imgUploadPath)) {
        $img = $imgName;
    } else {
        echo json_encode(['success' => false, 'message' => 'Image upload failed.']);
        exit;
    }
}

// Build SQL dynamically based on what is being updated
$fields = [];
$params = [];
$types = '';

if ($username !== '') {
    $fields[] = 'username = ?';
    $params[] = $username;
    $types .= 's';
}
if ($img !== null) {
    $fields[] = 'img = ?';
    $params[] = $img;
    $types .= 's';
}
if ($password !== '') {
    $fields[] = 'password = ?';
    $params[] = password_hash($password, PASSWORD_DEFAULT);
    $types .= 's';
}

if (empty($fields)) {
    echo json_encode(['success' => false, 'message' => 'No changes to update.']);
    exit;
}

$params[] = $user_id;
$types .= 'i';

$sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}
$stmt->close();
$conn->close();
exit;