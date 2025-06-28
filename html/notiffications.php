<?php
session_start();
include("../lib/functions.php");
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['role'] !== 'patient') {
    header("Location: index.php");
    exit();
}

// Database connection
require_once '../lib/conn.php';

// Get patient ID from session
$patient_id = $_SESSION['user_id'];

// Call stored procedure to get patient appointments
$stmt = $conn->prepare("CALL patient_appointments(?)");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Mark notifications as read
$conn->query("UPDATE notifications SET is_read = 1 WHERE patient_id = $patient_id AND type = 'appointment'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Appointments</title>
  
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
  
  <!-- Icons -->
  <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <!-- Page CSS -->
  <style>
    :root {
      --primary-color: #7367f0;
      --primary-hover: #5d52d1;
      --secondary-color: #82868b;
      --success-color: #28c76f;
      --info-color: #00cfe8;
      --warning-color: #ff9f43;
      --danger-color: #ea5455;
    }
    
    /* Appointment Cards */
    .appointment-card {
      border-left: 4px solid var(--primary-color);
      transition: all 0.3s;
      margin-bottom: 1.5rem;
    }
    
    .appointment-card.unread {
      background-color: rgba(255, 159, 67, 0.05);
      border-left-color: var(--warning-color);
    }
    
    .appointment-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
    }
    
    .status-badge {
      padding: 0.35rem 0.85rem;
      border-radius: 2rem;
      font-size: 0.85rem;
      font-weight: 500;
    }
    
    .status-scheduled {
      background-color: rgba(255, 159, 67, 0.12);
      color: var(--warning-color);
    }
    
    .status-completed {
      background-color: rgba(40, 199, 111, 0.12);
      color: var(--success-color);
    }
    
    .status-cancelled {
      background-color: rgba(234, 84, 85, 0.12);
      color: var(--danger-color);
    }
    
    .meeting-url {
      word-break: break-all;
      color: var(--primary-color);
    }
    
    .meeting-url:hover {
      color: var(--primary-hover);
      text-decoration: underline;
    }
    
    /* Empty State */
    .empty-state {
      background-color: #fff;
      border-radius: 0.5rem;
      padding: 3rem;
      text-align: center;
    }
    
    /* Action Buttons */
    .action-btns .btn {
      padding: 0.375rem 0.75rem;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
      .action-btns {
        flex-direction: column;
        gap: 0.5rem;
      }
    }
  </style>
</head>
<body>
  <!-- Layout wrapper with proper structure -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Sidebar -->
      <?php include("../lib/pSidebar.php"); ?>
      
      <!-- Layout page -->
      <div class="layout-page">
        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row mb-3">
              <div class="col-12">
                <h4 class="fw-bold py-3 mb-4">
                  <span class="text-muted fw-light">Patient /</span> My Appointments
                </h4>
              </div>
            </div>

            <?php if (empty($appointments)): ?>
              <div class="card empty-state">
                <div class="card-body text-center py-5">
                  <i class="bi bi-calendar-x display-4 text-muted mb-4"></i>
                  <h3>No appointments scheduled</h3>
                  <p class="text-muted mb-4">You don't have any upcoming appointments</p>
                  <a href="../pages/appointments.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i> Book an Appointment
                  </a>
                </div>
              </div>
            <?php else: ?>
              <div class="row">
                <div class="col-lg-8 mb-4">
                  <?php foreach ($appointments as $appointment): ?>
                    <div class="card appointment-card mb-3 <?= $appointment['is_read'] ? '' : 'unread' ?>">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                          <h5 class="card-title mb-0">
                            <i class="bi bi-person-badge me-2"></i>
                            Dr. <?= htmlspecialchars($appointment['doctor_name']) ?>
                            <?php if (!$appointment['is_read']): ?>
                              <span class="badge bg-warning ms-2">New</span>
                            <?php endif; ?>
                          </h5>
                          <span class="status-badge status-<?= $appointment['status'] ?>">
                            <?= ucfirst($appointment['status']) ?>
                          </span>
                        </div>
                        
                        <div class="mb-3">
                          <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-calendar-event me-2 text-muted"></i>
                            <span>Date: <?= htmlspecialchars($appointment['appointment_date']) ?></span>
                          </div>
                          <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-clock me-2 text-muted"></i>
                            <span>Time: <?= htmlspecialchars($appointment['appointment_time']) ?></span>
                          </div>
                          <div class="d-flex align-items-center">
                            <i class="bi bi-stopwatch me-2 text-muted"></i>
                            <span>Duration: <?= htmlspecialchars($appointment['duration']) ?> minutes</span>
                          </div>
                        </div>
                        
                        <?php if (!empty($appointment['link'])): ?>
                          <div class="alert alert-info mt-3">
                            <h6 class="d-flex align-items-center">
                              <i class="bi bi-link-45deg me-2"></i> Meeting Information
                            </h6>
                            <p class="mb-2">Click the link below to join your virtual appointment:</p>
                            <a href="<?= htmlspecialchars($appointment['link']) ?>" target="_blank" class="meeting-url d-block mb-2">
                              <?= htmlspecialchars($appointment['link']) ?>
                            </a>
                            <div class="d-flex flex-wrap gap-2">
                              <a href="<?= htmlspecialchars($appointment['link']) ?>" target="_blank" class="btn btn-success btn-sm">
                                <i class="bi bi-camera-video me-1"></i> Join Meeting
                              </a>
                              <button class="btn btn-outline-secondary btn-sm copy-link" data-url="<?= htmlspecialchars($appointment['link']) ?>">
                                <i class="bi bi-clipboard me-1"></i> Copy Link
                              </button>
                            </div>
                          </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                          <small class="text-muted">
                            <i class="bi bi-calendar-plus me-1"></i> Created: <?= date('M j, Y g:i A', strtotime($appointment['created_at'])) ?>
                          </small>
                          <?php if ($appointment['status'] === 'scheduled'): ?>
                            <div class="action-btns d-flex gap-2">
                              <button class="btn btn-outline-primary btn-sm edit-appointment" 
                                      data-id="<?= $appointment['id'] ?>">
                                <i class="bi bi-pencil me-1"></i> Reschedule
                              </button>
                              <button class="btn btn-outline-danger btn-sm cancel-appointment" 
                                      data-id="<?= $appointment['id'] ?>">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                              </button>
                            </div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
                
                <div class="col-lg-4">
                  <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i> Appointment Summary
                      </h5>
                    </div>
                    <div class="card-body">
                      <?php 
                      $upcoming = array_filter($appointments, function($a) {
                        return $a['status'] === 'scheduled' && 
                               strtotime($a['appointment_date'] . ' ' . $a['appointment_time']) > time();
                      });
                      
                      $completed = array_filter($appointments, function($a) {
                        return $a['status'] === 'completed';
                      });
                      ?>
                      
                      <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                          <i class="bi bi-calendar-check me-2 text-primary"></i>
                          <h6 class="mb-0">Upcoming Appointments</h6>
                        </div>
                        <p class="ms-4">You have <strong class="text-primary"><?= count($upcoming) ?></strong> upcoming appointment(s).</p>
                      </div>
                      
                      <div class="mb-4">
                        <div class="d-flex align-items-center mb-2">
                          <i class="bi bi-check-circle me-2 text-success"></i>
                          <h6 class="mb-0">Completed Appointments</h6>
                        </div>
                        <p class="ms-4">You've completed <strong class="text-success"><?= count($completed) ?></strong> appointment(s).</p>
                      </div>
                      
                      <div class="d-grid gap-2">
                        <a href="../pages/appointments.php" class="btn btn-primary">
                          <i class="bi bi-plus-circle me-2"></i> Book New Appointment
                        </a>
                        <a href="medical_history.php" class="btn btn-outline-secondary">
                          <i class="bi bi-file-medical me-2"></i> View Medical History
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
          <!-- / Content -->
          
          <!-- Footer (if needed) -->
          <footer class="content-footer footer bg-footer-theme">
            <!-- Footer content -->
          </footer>
          
          <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>
  </div>
  <!-- / Layout wrapper -->

  <!-- Reschedule Modal -->
  <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Reschedule Appointment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="rescheduleForm">
          <div class="modal-body">
            <input type="hidden" id="appointmentId" name="id">
            <div class="mb-3">
              <label for="newDate" class="form-label">New Date</label>
              <input type="date" class="form-control" id="newDate" name="appointment_date" required>
            </div>
            <div class="mb-3">
              <label for="newTime" class="form-label">New Time</label>
              <input type="time" class="form-control" id="newTime" name="appointment_time" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
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

  <script>
    // Copy meeting link to clipboard
    document.querySelectorAll('.copy-link').forEach(btn => {
      btn.addEventListener('click', function() {
        const url = this.getAttribute('data-url');
        navigator.clipboard.writeText(url).then(() => {
          const originalText = this.innerHTML;
          this.innerHTML = '<i class="bi bi-check2 me-1"></i> Copied!';
          setTimeout(() => {
            this.innerHTML = originalText;
          }, 2000);
        });
      });
    });

    // Reschedule appointment
    document.querySelectorAll('.edit-appointment').forEach(btn => {
      btn.addEventListener('click', function() {
        const appointmentId = this.getAttribute('data-id');
        document.getElementById('appointmentId').value = appointmentId;
        
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('newDate').min = today;
        
        const modal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
        modal.show();
      });
    });

    // Cancel appointment
    document.querySelectorAll('.cancel-appointment').forEach(btn => {
      btn.addEventListener('click', function() {
        if (confirm('Are you sure you want to cancel this appointment?')) {
          const appointmentId = this.getAttribute('data-id');
          fetch('cancel_appointment.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: appointmentId })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              location.reload();
            } else {
              alert(data.message || 'Failed to cancel appointment');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while cancelling the appointment');
          });
        }
      });
    });

    // Handle reschedule form submission
    document.getElementById('rescheduleForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      
      fetch('reschedule_appointment.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          alert(data.message || 'Failed to reschedule appointment');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while rescheduling the appointment');
      });
    });
  </script>
</body>
</html>