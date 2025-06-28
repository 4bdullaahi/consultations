<?php
session_start();
require_once("../lib/conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitize & collect inputs
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $specialization = trim($_POST['specialization']);
    $price = trim($_POST['price']);
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
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = $_POST['email'];

    // 2. Hash password
    // $password = password_hash($rawPassword, PASSWORD_DEFAULT);

    // 3. Handle image upload
   

    // 4. Call the stored procedure
    $stmt = $conn->prepare("CALL doctors_sp(?, ?, ?, ?, ?, ?,?,?)");
    if ($stmt) {
        $stmt->bind_param("ssssssss", $username, $password,$specialization, $price, $img, $address, $phone,$email);

        if ($stmt->execute()) {
            $stmt->store_result();
            $stmt->bind_result($resultMessage);
            $stmt->fetch();

            // On success:
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Doctor registered successfully.']);
            exit;
        } else {
            // On error:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error executing procedure: ' . $stmt->error]);
            exit;
        }

        $stmt->close();
    } else {
        // On error:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement.']);
        exit;
    }

    $conn->close();
}
?>
