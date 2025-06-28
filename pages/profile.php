<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/index.php");
    exit();
}

require_once("../lib/conn.php");
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, created_at, role, img FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $created_at, $role, $img);
$stmt->fetch();
$stmt->close();
$conn->close();

// Format the creation date nicely
$formatted_date = date('F j, Y', strtotime($created_at));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile | Health Portal</title>
  
  <!-- Favicon -->
  <link rel="shortcut icon" href="../assets/img/favicon.ico" type="image/x-icon">
  
  <!-- Fonts & Icons -->
  <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="../assets/vendor/fonts/fontawesome.css" />
  <link rel="stylesheet" href="../assets/vendor/fonts/flag-icons.css" />
  <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />
  
  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />
  
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <style>
    :root {
      --primary-color: #7367F0;
      --primary-hover: #5E50EE;
      --secondary-color: #82868B;
      --success-color: #28C76F;
      --border-radius: 0.5rem;
      --box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
    }
    
    body {
      font-family: 'Inter', sans-serif;
      background-color: #F8F8F8;
      color: #4E4B66;
    }
    
    .profile-container {
      max-width: 800px;
      margin: 2rem auto;
      padding: 0 1rem;
    }
    
    .profile-card {
      background: #FFFFFF;
      border-radius: var(--border-radius);
      box-shadow: var(--box-shadow);
      overflow: hidden;
      transition: all 0.3s ease;
    }
    
    .profile-card:hover {
      box-shadow: 0 8px 32px 0 rgba(34, 41, 47, 0.15);
    }
    
    .profile-header {
      background: linear-gradient(135deg, var(--primary-color), #A66FFE);
      color: white;
      padding: 2rem;
      text-align: center;
    }
    
    .profile-avatar {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid rgba(255,255,255,0.3);
      margin-bottom: 1rem;
      transition: all 0.3s ease;
    }
    
    .profile-avatar:hover {
      transform: scale(1.05);
    }
    
    .profile-name {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .profile-role {
      display: inline-block;
      background: rgba(255,255,255,0.2);
      padding: 0.25rem 1rem;
      border-radius: 2rem;
      font-size: 0.875rem;
      font-weight: 500;
    }
    
    .profile-body {
      padding: 2rem;
    }
    
    .form-label {
      font-weight: 500;
      color: #5E5873;
      margin-bottom: 0.5rem;
    }
    
    .form-control {
      border-radius: var(--border-radius);
      padding: 0.75rem 1rem;
      border: 1px solid #DBDADE;
      transition: all 0.3s ease;
    }
    
    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 3px 10px 0 rgba(115, 103, 240, 0.1);
    }
    
    .form-control[disabled] {
      background-color: #F5F5F5;
      opacity: 1;
    }
    
    .btn-primary {
      background-color: var(--primary-color);
      border: none;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      letter-spacing: 0.5px;
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      background-color: var(--primary-hover);
      transform: translateY(-2px);
    }
    
    .file-upload {
      position: relative;
      display: inline-block;
      width: 100%;
    }
    
    .file-upload-label {
      display: block;
      padding: 0.75rem;
      background: #F5F5F5;
      border-radius: var(--border-radius);
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .file-upload-label:hover {
      background: #EAEAEA;
    }
    
    .file-upload-input {
      position: absolute;
      left: 0;
      top: 0;
      opacity: 0;
      width: 100%;
      height: 100%;
      cursor: pointer;
    }
    
    .info-item {
      margin-bottom: 1.5rem;
    }
    
    .info-value {
      font-weight: 400;
      color: #6E6B7B;
    }
    
    @media (max-width: 768px) {
      .profile-header {
        padding: 1.5rem;
      }
      
      .profile-avatar {
        width: 100px;
        height: 100px;
      }
    }
  </style>
</head>
<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <?php
        // Sidebar selection based on user role
        if (isset($_SESSION['role'])) {
          switch ($_SESSION['role']) {
            case 'admin':
              include("../lib/sidebar.php");
              break;
            case 'doctor':
              include("../lib/docSide.php");
              break;
            case 'patient':
              include("../lib/pSidebar.php");
              break;
            default:
              // Optionally handle unknown roles
              break;
          }
        }
      ?>
      <div class="layout-page">
        <div class="content-wrapper">
          <div class="profile-container">
            <div class="profile-card">
              <div class="profile-header">
                <img src="../uploads/<?= htmlspecialchars($img ?? 'default.png') ?>" alt="Profile" class="profile-avatar" id="avatarPreview">
                <h2 class="profile-name"><?= htmlspecialchars($username) ?></h2>
                <span class="profile-role"><?= ucfirst(htmlspecialchars($role)) ?></span>
              </div>
              
              <div class="profile-body">
                <form id="profileForm" action="../updates/updateProfile.php" method="POST" enctype="multipart/form-data">
                  <div class="mb-4">
                    <label class="form-label">Profile Picture</label>
                    <div class="file-upload">
                      <label class="file-upload-label" for="photo">
                        <i class="bx bx-cloud-upload me-2"></i>Choose a new photo
                      </label>
                      <input type="file" name="photo" id="photo" class="file-upload-input" accept="image/*">
                    </div>
                    <small class="text-muted">Max file size: 2MB (JPEG, PNG)</small>
                  </div>
                  
                  <div class="mb-4 info-item">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($username) ?>" required>
                  </div>
                  
                  <div class="mb-4 info-item">
                    <label class="form-label">Member Since</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($formatted_date) ?>" disabled>
                  </div>
                  
                  <div class="mb-4 info-item">
                    <label class="form-label">Account Type</label>
                    <input type="text" class="form-control" value="<?= ucfirst(htmlspecialchars($role)) ?>" disabled>
                  </div>
                  
                  <div class="mb-4 info-item">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter new password (leave blank to keep current)">
                    <small class="text-muted">Password must be at least 8 characters long</small>
                  </div>
                  
                  <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">
                      <i class="bx bx-save me-2"></i>Save Changes
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Preview uploaded image
    const avatarPreview = document.getElementById('avatarPreview');
    const fileInput = document.getElementById('photo');
    
    fileInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          avatarPreview.src = e.target.result;
        }
        
        reader.readAsDataURL(this.files[0]);
      }
    });
    
    // Form submission with validation
    const form = document.getElementById('profileForm');
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Validate username
      const username = form.username.value.trim();
      if (!/^[A-Za-z\s]+$/.test(username)) {
        Swal.fire({
          icon: 'error',
          title: 'Invalid Username',
          text: 'Username can only contain letters and spaces.'
        });
        return;
      }
      
      // Check file size if image is being uploaded
      if (fileInput.files[0] && fileInput.files[0].size > 2 * 1024 * 1024) {
        Swal.fire({
          icon: 'error',
          title: 'File Too Large',
          text: 'Profile picture must be less than 2MB.'
        });
        return;
      }
      
      // Show loading state
      const submitBtn = form.querySelector('button[type="submit"]');
      const originalBtnText = submitBtn.innerHTML;
      submitBtn.innerHTML = '<i class="bx bx-loader bx-spin me-2"></i>Updating...';
      submitBtn.disabled = true;
      
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
            title: 'Profile Updated!',
            text: data.message || 'Your changes have been saved successfully.',
            showConfirmButton: false,
            timer: 2000
          }).then(() => {
            if (data.redirect) {
              window.location.href = data.redirect;
            } else {
              window.location.reload();
            }
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Update Failed',
            text: data.message || 'There was an error updating your profile.'
          });
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'An unexpected error occurred. Please try again.'
        });
      })
      .finally(() => {
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
      });
    });
    
    // Prevent typing non-letters in username
    form.username.addEventListener('input', function() {
      this.value = this.value.replace(/[^A-Za-z\s]/g, '');
    });
  });
  </script>
</body>
</html>