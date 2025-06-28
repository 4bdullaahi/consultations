<?php
session_start();
require_once '../lib/conn.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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
if (!isset($_POST['id'], $_POST['username'], $_POST['role'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

// Sanitize input
$id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
$role = filter_var($_POST['role'], FILTER_SANITIZE_STRING);

// Check valid role
$allowed_roles = ['admin', 'doctor', 'patient'];
if (!in_array($role, $allowed_roles)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role specified']);
    exit();
}

// Image handling
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    $uploadDir = '../uploads/';
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fileInfo, $file['tmp_name']);
    finfo_close($fileInfo);

    if (!in_array($mimeType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid image type']);
        exit();
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Image size too large (max 2MB)']);
        exit();
    }

    // Unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid('user_', true) . '.' . $ext;
    $targetPath = $uploadDir . $newFileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
        exit();
    }

    // Save relative path (e.g., uploads/filename.jpg)
    $imagePath = '../uploads/' . $newFileName;
}

try {
    if ($imagePath) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ?, img = ? WHERE id = ?");
        $stmt->bind_param("sssi", $username, $role, $imagePath, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $role, $id);
    }

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => [
                'id' => $id,
                'username' => $username,
                'role' => $role,
                'img' => $imagePath
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made or user not found']);
    }

    $stmt->close();
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
    } else {
        error_log("DB error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}

$conn->close();
exit;
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('updateUserForm'); // Change to your form's ID
  if (!form) return;
  form.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch(form.action, {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'User updated!',
          text: data.message || 'User updated successfully.'
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.message || 'Failed to update user.'
        });
      }
    })
    .catch(error => {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'An error occurred while updating user.'
      });
    });
  });
});
</script>
