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
$status_filter = $_GET['status'] ?? '';
$search_term = $_GET['search'] ?? '';

// Call the stored procedure with status filter
$stmt = $conn->prepare("CALL appointments_rp(?)");
$stmt->bind_param("s", $status_filter);
$stmt->execute();
$result = $stmt->get_result();

// Filter results based on search term if provided
$filtered_results = [];
while ($row = $result->fetch_assoc()) {
    if (empty($search_term) || 
        stripos($row['doctor_name'], $search_term) !== false ||
        stripos($row['patient_id'], $search_term) !== false ||
        stripos($row['appointment_date'], $search_term) !== false ||
        stripos($row['appointment_time'], $search_term) !== false ||
        stripos($row['status'], $search_term) !== false ||
        stripos($row['payment_status'], $search_term) !== false) {
        $filtered_results[] = $row;
    }
}
$stmt->close();

// Function to highlight search terms
function highlight_search_term($text, $term) {
    if (empty($term)) return htmlspecialchars($text);
    return preg_replace("/(" . preg_quote($term) . ")/i", '<span class="bg-warning">$1</span>', htmlspecialchars($text));
}
?>
<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Appointments Report | Dashboard</title>

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
    .status-badge {
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 500;
    }
    .status-scheduled {
      background-color: #fff8e1;
      color: #ff8f00;
    }
    .status-completed {
      background-color: #e8f5e9;
      color: #2e7d32;
    }
    .status-cancelled {
      background-color: #ffebee;
      color: #c62828;
    }
    .payment-status {
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 500;
    }
    .payment-paid {
      background-color: #e8f5e9;
      color: #2e7d32;
    }
    .payment-pending {
      background-color: #fff8e1;
      color: #ff8f00;
    }
    .payment-failed {
      background-color: #ffebee;
      color: #c62828;
    }
    .table-responsive {
      padding: 0 20px;
    }
    .form-icon {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      left: 15px;
      color: #6c757d;
    }
    .form-icon-input {
      padding-left: 40px;
    }
    .bg-warning {
      background-color: #ffc107 !important;
      padding: 0 2px;
      border-radius: 3px;
    }
    .no-results {
      min-height: 300px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  </style>
</head>
<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <?php include("../lib/sidebar.php"); ?>
      
      <div class="layout-page">
        <div class="content-wrapper">
          <div class="card"> 
            <h5 class="card-header">Appointments Report</h5>
            <div class="content-wrapper main-content px-3 py-4">
              <form method="GET" action="" class="row g-2 align-items-center mb-4">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Search appointments..." 
                      value="<?= htmlspecialchars($search_term) ?>">
                  </div>
                </div>

                <div class="col-md-3">
                  <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="scheduled" <?= $status_filter === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                    <option value="completed" <?= $status_filter === 'completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                  </select>
                </div>

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
              <div id="appointments-table">
                <?php if (empty($filtered_results)): ?>
                  <div class="no-results">
                    <div class="text-center py-5">
                      <i class="bi bi-calendar-x" style="font-size: 3rem; color: #6c757d;"></i>
                      <h5 class="mt-3">No appointments found</h5>
                      <p class="text-muted">Try adjusting your search or filter criteria</p>
                      <a href="?" class="btn btn-primary mt-2">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset filters
                      </a>
                    </div>
                  </div>
                <?php else: ?>
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Patient ID</th>
                        <th>Doctor Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Amount</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($filtered_results as $appointment): ?>
                        <tr>
                          <td><?= $appointment['id'] ?></td>
                          <td><?= highlight_search_term($appointment['patient_id'], $search_term) ?></td>
                          <td><?= highlight_search_term($appointment['doctor_name'], $search_term) ?></td>
                          <td><?= highlight_search_term($appointment['appointment_date'], $search_term) ?></td>
                          <td><?= highlight_search_term($appointment['appointment_time'], $search_term) ?></td>
                          <td><?= $appointment['duration'] ?> mins</td>
                          <td>
                            <span class="status-badge status-<?= $appointment['status'] ?>">
                              <?= highlight_search_term(ucfirst($appointment['status']), $search_term) ?>
                            </span>
                          </td>
                          <td>
                            <span class="payment-status payment-<?= strtolower($appointment['payment_status']) ?>">
                              <?= highlight_search_term(ucfirst($appointment['payment_status']), $search_term) ?>
                            </span>
                          </td>
                          <td>$<?= number_format($appointment['amount'], 2) ?></td>
                          <td>
                            <button class="btn btn-sm btn-icon edit-btn"  
                                    data-appointment='<?= htmlspecialchars(json_encode($appointment), ENT_QUOTES, 'UTF-8') ?>'
                                    data-bs-toggle="modal" data-bs-target="#editAppointmentModal">
                              <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-icon delete-btn" 
                                    data-appointment-id="<?= $appointment['id'] ?>" 
                                    data-bs-toggle="modal" data-bs-target="#deleteAppointmentModal">
                              <i class="bi bi-trash"></i>
                            </button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Edit Appointment Modal -->
          <div class="modal fade" id="editAppointmentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-lg-custom">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title"><i class="bi bi-calendar2-event me-2"></i> Edit Appointment</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editAppointmentForm" method="POST" action="../updates/update_appointment.php">
                  <input type="hidden" id="editAppointmentId" name="id">
                  <div class="modal-body">
                    <div class="row mb-3">
                      <div class="col-md-6 mb-3">
                        <label for="editPatientId" class="form-label">Patient ID</label>
                        <div class="position-relative">
                          <i class="bi bi-person form-icon"></i>
                          <input type="text" id="editPatientId" name="patient_id" class="form-control form-icon-input" required>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="editDoctorName" class="form-label">Doctor Name</label>
                        <div class="position-relative">
                          <i class="bi bi-person-badge form-icon"></i>
                          <input type="text" id="editDoctorName" name="doctor_name" class="form-control form-icon-input" required>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row mb-3">
                      <div class="col-md-6 mb-3">
                        <label for="editAppointmentDate" class="form-label">Date</label>
                        <div class="position-relative">
                          <i class="bi bi-calendar form-icon"></i>
                          <input type="date" id="editAppointmentDate" name="appointment_date" class="form-control form-icon-input" required>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="editAppointmentTime" class="form-label">Time</label>
                        <div class="position-relative">
                          <i class="bi bi-clock form-icon"></i>
                          <input type="time" id="editAppointmentTime" name="appointment_time" class="form-control form-icon-input" required>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row mb-3">
                      <div class="col-md-4 mb-3">
                        <label for="editDuration" class="form-label">Duration (minutes)</label>
                        <div class="position-relative">
                          <i class="bi bi-stopwatch form-icon"></i>
                          <input type="number" id="editDuration" name="duration" class="form-control form-icon-input" min="15" step="15" required>
                        </div>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label for="editStatus" class="form-label">Status</label>
                        <select id="editStatus" name="status" class="form-select" required>
                          <option value="scheduled">Scheduled</option>
                          <option value="completed">Completed</option>
                          <option value="cancelled">Cancelled</option>
                        </select>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label for="editPaymentStatus" class="form-label">Payment Status</label>
                        <select id="editPaymentStatus" name="payment_status" class="form-select" required>
                          <option value="paid">Paid</option>
                          <option value="pending">Pending</option>
                          <option value="failed">Failed</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="row mb-3">
                      <div class="col-md-6 mb-3">
                        <label for="editAmount" class="form-label">Amount</label>
                        <div class="input-group">
                          <span class="input-group-text">$</span>
                          <input type="number" id="editAmount" name="amount" class="form-control" min="0" step="0.01" required>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <!-- Delete Appointment Modal -->
          <div class="modal fade" id="deleteAppointmentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i> Confirm Deletion</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteAppointmentForm" method="POST" action="../Delete/delete_appointment.php">
                  <input type="hidden" id="deleteAppointmentId" name="id">
                  <div class="modal-body">
                    <p>Are you sure you want to delete this appointment? This action cannot be undone.</p>
                    <div class="alert alert-warning" role="alert">
                      <i class="bi bi-exclamation-circle me-2"></i> Deleting this appointment will remove all related records.
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Appointment</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
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
    // Initialize tooltips
    $(function () {
      $('[data-bs-toggle="tooltip"]').tooltip();
    });
    
    // Edit button click handler
    document.querySelectorAll('.edit-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const appointmentData = JSON.parse(this.getAttribute('data-appointment'));
        
        // Populate modal fields
        document.getElementById('editAppointmentId').value = appointmentData.id;
        document.getElementById('editPatientId').value = appointmentData.patient_id;
        document.getElementById('editDoctorName').value = appointmentData.doctor_name;
        document.getElementById('editAppointmentDate').value = appointmentData.appointment_date;
        document.getElementById('editAppointmentTime').value = appointmentData.appointment_time;
        document.getElementById('editDuration').value = appointmentData.duration;
        document.getElementById('editStatus').value = appointmentData.status;
        document.getElementById('editPaymentStatus').value = appointmentData.payment_status.toLowerCase();
        document.getElementById('editAmount').value = appointmentData.amount;
        
        // Set modal title
        document.querySelector('#editAppointmentModal .modal-title').innerHTML = 
          `<i class="bi bi-calendar2-event me-2"></i> Edit Appointment #${appointmentData.id}`;
      });
    });
    
    // Delete button click handler
    document.querySelectorAll('.delete-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const appointmentId = this.getAttribute('data-appointment-id');
        document.getElementById('deleteAppointmentId').value = appointmentId;
        
        // Set modal title
        document.querySelector('#deleteAppointmentModal .modal-title').innerHTML = 
          `<i class="bi bi-exclamation-triangle me-2"></i> Delete Appointment #${appointmentId}`;
      });
    });

    // Form validation for edit form
    document.getElementById('editAppointmentForm').addEventListener('submit', function(e) {
      const amount = document.getElementById('editAmount').value;
      if (amount < 0) {
        e.preventDefault();
        alert('Amount cannot be negative');
      }
      
      const date = new Date(document.getElementById('editAppointmentDate').value);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      
      if (date < today) {
        e.preventDefault();
        alert('Appointment date cannot be in the past');
      }
    });

    function reloadAppointmentsTable() {
  const params = new URLSearchParams(window.location.search);
  fetch('appointmentReport.php?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(res => res.text())
    .then(html => {
      // Extract only the table part from the response
      const parser = new DOMParser();
      const doc = parser.parseFromString(html, 'text/html');
      const newTable = doc.getElementById('appointments-table');
      if (newTable) {
        document.getElementById('appointments-table').innerHTML = newTable.innerHTML;
        attachActionHandlers(); // Re-attach handlers to new buttons
      }
    });
}

// Call this after update or delete success
function attachActionHandlers() {
  // Edit button handler (if you want to re-attach)
  document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const appointmentData = JSON.parse(this.getAttribute('data-appointment'));
      // ...populate modal fields as before...
    });
  });

  // Delete button handler
  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const appointmentId = this.getAttribute('data-appointment-id');
      document.getElementById('deleteAppointmentId').value = appointmentId;
      // ...set modal title as before...
    });
  });
}

// Initial attach
attachActionHandlers();

document.getElementById('deleteAppointmentForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch(this.action, {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      // Hide modal
      var modal = bootstrap.Modal.getInstance(document.getElementById('deleteAppointmentModal'));
      modal.hide();
      reloadAppointmentsTable();
    } else {
      alert(data.message);
    }
  });
});

document.getElementById('editAppointmentForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch(this.action, {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      // Hide modal
      var modal = bootstrap.Modal.getInstance(document.getElementById('editAppointmentModal'));
      modal.hide();
      reloadAppointmentsTable();
    } else {
      alert(data.message);
    }
  });
});
  </script>
</body>
</html>

