<?php
session_start();
require_once("../lib/conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $rawPassword = trim($_POST['password']);
    $img = '';
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
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Image upload failed.']);
            exit;
        }
    }

    $password = password_hash($rawPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("CALL admins_sp(?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $username, $password, $img);

        if ($stmt->execute()) {
            $stmt->store_result();
            $stmt->bind_result($resultMessage);
            $stmt->fetch();

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => $resultMessage]);
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Error executing procedure: ' . $stmt->error]);
            exit;
        }
        $stmt->close();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement.']);
        exit;
    }
    $conn->close();
}
?>
