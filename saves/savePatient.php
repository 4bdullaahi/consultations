<?php
session_start();
require_once("../lib/conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitize & collect inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $dob = $_POST['DOB'];

    // 2. Hash password
    // $password = password_hash($rawPassword, PASSWORD_DEFAULT);

    // 3. Handle image upload
    $img = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $imgTmpPath = $_FILES['photo']['tmp_name'];
        $imgName = time() . '_' . basename($_FILES['photo']['name']);
        $uploadDir = '../uploads/';
        $imgUploadPath = $uploadDir . $imgName;

        // Ensure upload folder exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file
        if (move_uploaded_file($imgTmpPath, $imgUploadPath)) {
            $img = $imgName;
        } else {
            echo "<script>alert('Image upload failed.'); window.history.back();</script>";
            exit;
        }
    }

    // 4. Call the stored procedure
    $stmt = $conn->prepare("CALL patients_sp(?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssssss", $username, $password, $img, $address, $phone, $dob);

        if ($stmt->execute()) {
            $stmt->store_result();
            $stmt->bind_result($resultMessage);
            $stmt->fetch();
            echo json_encode(['success' => true, 'message' => $resultMessage]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error executing procedure: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo "<script>alert('Failed to prepare statement.'); window.history.back();</script>";
    }

    $conn->close();
}
?>
