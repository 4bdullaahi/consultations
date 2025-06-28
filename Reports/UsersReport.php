<?php
session_start();
include("../lib/functions.php");
if (!isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Database connection
require_once '../lib/conn.php';

// Get filter parameters
$role_filter = $_GET['role'] ?? '';
$search_term = $_GET['search'] ?? '';

// Call the stored procedure
$stmt = $conn->prepare("CALL users_rp(?)");
$stmt->bind_param("s", $role_filter);
$stmt->execute();
$result = $stmt->get_result();

// Filter results based on search term if provided
$filtered_results = [];
while ($row = $result->fetch_assoc()) {
    if (empty($search_term) || 
        stripos($row['username'], $search_term) !== false) {
        $filtered_results[] = $row;
    }
}
$stmt->close();

?>
<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Users Report | Dashboard</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

  <!-- Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>
  
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  
  <style>
    .modal-lg-custom {
      max-width: 800px;
    }
    .user-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin: 0 auto 15px;
      display: block;
      border: 3px solid #e9ecef;
    }
    .section-divider {
      border-top: 1px solid #e9ecef;
      margin: 20px 0;
    }
    .status-badge {
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 500;
    }
    .status-active {
      background-color: #e8f5e9;
      color: #2e7d32;
    }
    .status-inactive {
      background-color: #ffebee;
      color: #c62828;
    }
    .table-responsive {
      padding: 0 20px;
    }
    .card-header {
      padding: 1.5rem 1.5rem 0;
    }
    .main-content {
      padding-top: 0 !important;
    }
    .btn-icon {
      width: 32px;
      height: 32px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
    }
    .rounded-circle {
      object-fit: cover;
    }
  </style>
</head>
<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->
      <?php include("../lib/sidebar.php"); ?>
      <!-- / Menu -->
      <div class="layout-page">
        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="card"> 
            <h5 class="card-header">Users Report</h5>
            <div class="content-wrapper main-content px-3 py-4">
              <form method="GET" action="" class="row g-2 align-items-center mb-4">
                <!-- Search Input -->
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Search by username..." 
                      value="<?= htmlspecialchars($search_term) ?>">
                  </div>
                </div>

                <!-- Filter Dropdown -->
                <div class="col-md-3">
                  <select class="form-select" name="role">
                    <option value="">All Roles</option>
                    <option value="doctor" <?= $role_filter === 'doctor' ? 'selected' : '' ?>>Doctor</option>
                    <option value="patient" <?= $role_filter === 'patient' ? 'selected' : '' ?>>Patient</option>
                    <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Admin</option>
                  </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-md-3 d-flex gap-2">
                  <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-funnel me-1"></i> Filter
                  </button>
                  <a href="?" class="btn btn-secondary">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </a>
                </div>
              </form>
            </div>
             
            <div class="table-responsive text-nowrap">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($filtered_results as $user): ?>
                    <tr>
                      <td><?= $user['id'] ?></td>
                      <td>
                        <img src="../uploads/<?= $user['img'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($user['username']) ?>" 
                             alt="User Image" width="40" height="40" class="rounded-circle">
                      </td>
                      <td><?= htmlspecialchars($user['username']) ?></td>
                      <td><?= ucfirst($user['role']) ?></td>
                      <td><?= $user['created_at'] ?></td>
                      <td>
                        <button class="btn btn-sm btn-icon  edit-btn"  
                                data-user='<?= json_encode($user) ?>'
                                data-bs-toggle="modal" data-bs-target="#editUserModal">
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-icon  delete-btn" 
                                data-user-id="<?= $user['id'] ?>" 
                                data-user-name="<?= htmlspecialchars($user['username']) ?>">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
          <!-- / Content -->
          <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>
  <!-- / Layout wrapper -->

  <!-- Edit User Modal -->
  <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-lg-custom">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalTitle"><i class="bi bi-pencil-square me-2"></i> Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editUserForm" method="POST" action="../updates/updateusers.php" enctype="multipart/form-data">
          <input type="hidden" id="editUserId" name="id">
          <div class="modal-body">
            <div class="text-center mb-4">
              <img src="" class="user-avatar" id="userAvatar">
              <div class="d-flex justify-content-center gap-2 mt-3">
                <label for="userImageUpload" class="btn btn-sm btn-label-primary">
                  <i class="bi bi-camera me-1"></i> Change Photo
                  <input type="file" id="userImageUpload" name="image" class="d-none" accept="image/*">
                </label>
                <button type="button" class="btn btn-sm btn-label-secondary" id="removeImageBtn">
                  <i class="bi bi-trash me-1"></i> Remove
                </button>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-6 mb-3">
                <label for="editUsername" class="form-label">Username</label>
                <input type="text" id="editUsername" name="username" class="form-control" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="editRole" class="form-label">Role</label>
                <select id="editRole" name="role" class="form-select" required>
                  <option value="admin">Admin</option>
                  <option value="doctor">Doctor</option>
                  <option value="patient">Patient</option>
                </select>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-6 mb-3">
                <label for="editCreatedAt" class="form-label">Created At</label>
                <input type="text" id="editCreatedAt" class="form-control" disabled>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
              Cancel
            </button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Core JS -->
  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>
  <script src="../assets/js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <script>
    // Initialize tooltips
    $(function () {
      $('[data-bs-toggle="tooltip"]').tooltip();
    });
    
    // Edit button click handler
    document.querySelectorAll('.edit-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const userData = JSON.parse(this.getAttribute('data-user'));
        
        // Populate modal fields
        document.getElementById('editUserId').value = userData.id;
        document.getElementById('editUsername').value = userData.username;
        document.getElementById('editRole').value = userData.role;
        document.getElementById('editCreatedAt').value = userData.created_at;
        
        // Set avatar
        const avatarUrl = userData.img || `https://ui-avatars.com/api/?name=${encodeURIComponent(userData.username)}&background=0d6efd&color=fff`;
        document.getElementById('userAvatar').src = avatarUrl;
        
        // Set modal title
        document.getElementById('editUserModalTitle').innerHTML = 
          `<i class="bi bi-pencil-square me-2"></i> Edit ${userData.username}`;
      });
    });
    
    // Handle image upload preview
    document.getElementById('userImageUpload').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
          document.getElementById('userAvatar').src = event.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
    
    // Handle remove image button
    document.getElementById('removeImageBtn').addEventListener('click', function() {
      const username = document.getElementById('editUsername').value;
      document.getElementById('userAvatar').src = `https://ui-avatars.com/api/?name=${encodeURIComponent(username)}&background=0d6efd&color=fff`;
      document.getElementById('userImageUpload').value = '';
    });
    
    // Delete button handler
    document.querySelectorAll('.delete-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const userId = this.getAttribute('data-user-id');
        const userName = this.getAttribute('data-user-name');
        
        if(confirm(`Are you sure you want to delete ${userName}?`)) {
          fetch('../Delete/delete_User.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${userId}`
          })
          .then(response => response.json())
          .then(data => {
            if(data.success) {
              alert(`${userName} has been deleted.`);
              location.reload();
            } else {
              alert(`Error: ${data.message}`);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the user.');
          });
        }
      });
    });
    
    document.addEventListener('DOMContentLoaded', function() {
      const editForm = document.getElementById('editUserForm');
      if (editForm) {
        editForm.addEventListener('submit', function(e) {
          e.preventDefault();

          const formData = new FormData(editForm);

          fetch(editForm.action, {
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
              }).then(() => {
                // Hide the modal before reload to avoid aria-hidden/focus error
                const modal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
                if (modal) modal.hide();
                setTimeout(() => location.reload(), 300);
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
      }
    });
  </script>
</body>
</html>