<?php
session_start();
include("../lib/functions.php");
if (!isset($_SESSION['role'])) {
    header("Location: ../index.php");
    exit();
}
if ($_SESSION['role'] !== 'patient' && $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Database connection
require_once '../lib/conn.php';

// Get filter parameters
$billing_cycle_filter = $_GET['billing_cycle'] ?? '';
$user_id = $_SESSION['user_id'];
$search_term = $_GET['search'] ?? '';

// Call the stored procedure with billing cycle filter
$stmt = $conn->prepare("CALL patient_subscriptions_rp(?,?)");
$stmt->bind_param("ss",$user_id, $billing_cycle_filter);
$stmt->execute();
$result = $stmt->get_result();

// Filter results based on search term if provided
$filtered_results = [];
while ($row = $result->fetch_assoc()) {
    if (empty($search_term) || 
        stripos($row['username'], $search_term) !== false ||
        stripos($row['plan_name'], $search_term) !== false ||
        stripos($row['transaction_id'], $search_term) !== false ||
        stripos($row['billing_cycle'], $search_term) !== false ||
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

  <title>Subscriptions Report | Dashboard</title>

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
    .billing-cycle {
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 500;
    }
    .cycle-monthly {
      background-color: #e3f2fd;
      color: #1565c0;
    }
    .cycle-annual {
      background-color: #e8f5e9;
      color: #2e7d32;
    }
    .payment-status {
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 500;
    }
    .payment-active {
      background-color: #e8f5e9;
      color: #2e7d32;
    }
    .payment-cancelled {
      background-color: #ffebee;
      color: #c62828;
    }
    .payment-pending {
      background-color: #fff8e1;
      color: #ff8f00;
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
    .card-number {
      font-family: monospace;
    }
  </style>
</head>
<body>
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <?php include("../lib/pSidebar.php"); ?>
      
      <div class="layout-page">
        <div class="content-wrapper">
          <div class="card"> 
            <h5 class="card-header">Subscriptions Report</h5>
            <div class="content-wrapper main-content px-3 py-4">
              <form method="GET" action="" class="row g-2 align-items-center mb-4">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Search subscriptions..." 
                      value="<?= htmlspecialchars($search_term) ?>">
                  </div>
                </div>

                <div class="col-md-3">
                  <select class="form-select" name="billing_cycle">
                    <option value="">All Billing Cycles</option>
                    <option value="monthly" <?= $billing_cycle_filter === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                    <option value="annual" <?= $billing_cycle_filter === 'annual' ? 'selected' : '' ?>>Annual</option>
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
              <div id="subscriptions-table">
                <?php if (empty($filtered_results)): ?>
                  <div class="no-results">
                    <div class="text-center py-5">
                      <i class="bi bi-credit-card" style="font-size: 3rem; color: #6c757d;"></i>
                      <h5 class="mt-3">No subscriptions found</h5>
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
                        <th>Username</th>
                        <th>Plan Name</th>
                        <th>Price</th>
                        <th>Billing Cycle</th>
                        <!-- <th>Cardholder</th> -->
                        <th>Card Number</th>
                        <th>Expiry</th>
                        <!-- <th>Transaction ID</th> -->
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>Next Billing</th>
                        <!-- <th>Actions</th> -->
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($filtered_results as $subscription): ?>
                        <tr>
                          <td><?= $subscription['id'] ?></td>
                          <td><?= highlight_search_term($subscription['username'], $search_term) ?></td>
                          <td><?= highlight_search_term($subscription['plan_name'], $search_term) ?></td>
                          <td>$<?= number_format($subscription['plan_price'], 2) ?></td>
                          <td>
                            <span class="billing-cycle cycle-<?= $subscription['billing_cycle'] ?>">
                              <?= highlight_search_term(ucfirst($subscription['billing_cycle']), $search_term) ?>
                            </span>
                          </td>
                          <!-- <td><?= highlight_search_term($subscription['cardholder_name'], $search_term) ?></td> -->
                          <td class="card-number">•••• <?= $subscription['card_number'] ?></td>
                          <td><?= $subscription['expiry_date'] ?></td>
                          <!-- <td><?= highlight_search_term($subscription['transaction_id'], $search_term) ?></td> -->
                          <td>
                            <span class="payment-status payment-<?= strtolower($subscription['payment_status']) ?>">
                              <?= highlight_search_term(ucfirst($subscription['payment_status']), $search_term) ?>
                            </span>
                          </td>
                          <td><?= date('M d, Y', strtotime($subscription['start_date'])) ?></td>
                          <td><?= date('M d, Y', strtotime($subscription['next_billing_date'])) ?></td>
                          <!-- <td>
                            <button class="btn btn-sm btn-icon edit-btn"  
                                    data-subscription='<?= htmlspecialchars(json_encode($subscription), ENT_QUOTES, 'UTF-8') ?>'
                                    data-bs-toggle="modal" data-bs-target="#editSubscriptionModal">
                              <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-icon delete-btn" 
                                    data-subscription-id="<?= $subscription['id'] ?>" 
                                    data-bs-toggle="modal" data-bs-target="#deleteSubscriptionModal">
                              <i class="bi bi-trash"></i>
                            </button>
                          </td> -->
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Edit Subscription Modal -->
          <div class="modal fade" id="editSubscriptionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-lg-custom">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title"><i class="bi bi-credit-card me-2"></i> Edit Subscription</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editSubscriptionForm" method="POST" action="../updates/update_subscription.php">
                  <input type="hidden" id="editSubscriptionId" name="id">
                  <div class="modal-body">
                    <div class="row mb-3">
                      <div class="col-md-6 mb-3">
                        <label for="editUsername" class="form-label">Username</label>
                        <div class="position-relative">
                          <i class="bi bi-person form-icon"></i>
                          <input type="text" id="editUsername" class="form-control form-icon-input" readonly>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="editPlanName" class="form-label">Plan Name</label>
                        <div class="position-relative">
                          <i class="bi bi-card-checklist form-icon"></i>
                          <input type="text" id="editPlanName" name="plan_name" class="form-control form-icon-input" required>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row mb-3">
                      <div class="col-md-4 mb-3">
                        <label for="editPlanPrice" class="form-label">Price</label>
                        <div class="input-group">
                          <span class="input-group-text">$</span>
                          <input type="number" id="editPlanPrice" name="plan_price" class="form-control" min="0" step="0.01" required>
                        </div>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label for="editBillingCycle" class="form-label">Billing Cycle</label>
                        <select id="editBillingCycle" name="billing_cycle" class="form-select" required>
                          <option value="monthly">Monthly</option>
                          <option value="annual">Annual</option>
                        </select>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label for="editPaymentStatus" class="form-label">Payment Status</label>
                        <select id="editPaymentStatus" name="payment_status" class="form-select" required>
                          <option value="active">Active</option>
                          <option value="cancelled">Cancelled</option>
                          <option value="pending">Pending</option>
                        </select>
                      </div>
                    </div>
                    
                    <div class="row mb-3">
                      <!-- <div class="col-md-6 mb-3">
                        <label for="editCardholderName" class="form-label">Cardholder Name</label>
                        <div class="position-relative">
                          <i class="bi bi-person-badge form-icon"></i>
                          <input type="text" id="editCardholderName" name="cardholder_name" class="form-control form-icon-input" required>
                        </div>
                      </div> -->
                      <div class="col-md-6 mb-3">
                        <label for="editCardNumber" class="form-label">Card Number (Last 4)</label>
                        <div class="position-relative">
                          <i class="bi bi-credit-card form-icon"></i>
                          <input type="text" id="editCardNumber" name="card_number" class="form-control form-icon-input" maxlength="4" required>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row mb-3">
                      <div class="col-md-6 mb-3">
                        <label for="editExpiryDate" class="form-label">Expiry Date</label>
                        <div class="position-relative">
                          <i class="bi bi-calendar-event form-icon"></i>
                          <input type="text" id="editExpiryDate" name="expiry_date" class="form-control form-icon-input" placeholder="MM/YYYY" required>
                        </div>
                      </div>
                      <!-- <div class="col-md-6 mb-3">
                        <label for="editTransactionId" class="form-label">Transaction ID</label>
                        <div class="position-relative">
                          <i class="bi bi-receipt form-icon"></i>
                          <input type="text" id="editTransactionId" name="transaction_id" class="form-control form-icon-input" required>
                        </div>
                      </div> -->
                    </div>
                    
                    <div class="row mb-3">
                      <div class="col-md-6 mb-3">
                        <label for="editStartDate" class="form-label">Start Date</label>
                        <div class="position-relative">
                          <i class="bi bi-calendar form-icon"></i>
                          <input type="date" id="editStartDate" name="start_date" class="form-control form-icon-input" required>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="editNextBillingDate" class="form-label">Next Billing Date</label>
                        <div class="position-relative">
                          <i class="bi bi-calendar-check form-icon"></i>
                          <input type="date" id="editNextBillingDate" name="next_billing_date" class="form-control form-icon-input" required>
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

          <!-- Delete Subscription Modal -->
          <div class="modal fade" id="deleteSubscriptionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i> Confirm Deletion</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deleteSubscriptionForm" method="POST" action="../Delete/delete_subscription.php">
                  <input type="hidden" id="deleteSubscriptionId" name="id">
                  <div class="modal-body">
                    <p>Are you sure you want to delete this subscription? This action cannot be undone.</p>
                    <div class="alert alert-warning" role="alert">
                      <i class="bi bi-exclamation-circle me-2"></i> Deleting this subscription will remove all related records.
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Subscription</button>
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
        const subscriptionData = JSON.parse(this.getAttribute('data-subscription'));
        
        // Populate modal fields
        document.getElementById('editSubscriptionId').value = subscriptionData.id;
        document.getElementById('editUsername').value = subscriptionData.username;
        document.getElementById('editPlanName').value = subscriptionData.plan_name;
        document.getElementById('editPlanPrice').value = subscriptionData.plan_price;
        document.getElementById('editBillingCycle').value = subscriptionData.billing_cycle;
        document.getElementById('editPaymentStatus').value = subscriptionData.payment_status.toLowerCase();
        // document.getElementById('editCardholderName').value = subscriptionData.cardholder_name;
        document.getElementById('editCardNumber').value = subscriptionData.card_number;
        document.getElementById('editExpiryDate').value = subscriptionData.expiry_date;
        // document.getElementById('editTransactionId').value = subscriptionData.transaction_id;
        document.getElementById('editStartDate').value = subscriptionData.start_date.split(' ')[0];
        document.getElementById('editNextBillingDate').value = subscriptionData.next_billing_date.split(' ')[0];
        
        // Set modal title
        document.querySelector('#editSubscriptionModal .modal-title').innerHTML = 
          `<i class="bi bi-credit-card me-2"></i> Edit Subscription #${subscriptionData.id}`;
      });
    });
    
    // Delete button click handler
    document.querySelectorAll('.delete-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const subscriptionId = this.getAttribute('data-subscription-id');
        document.getElementById('deleteSubscriptionId').value = subscriptionId;
        
        // Set modal title
        document.querySelector('#deleteSubscriptionModal .modal-title').innerHTML = 
          `<i class="bi bi-exclamation-triangle me-2"></i> Delete Subscription #${subscriptionId}`;
      });
    });

    // Form validation for edit form
    document.getElementById('editSubscriptionForm').addEventListener('submit', function(e) {
      const price = document.getElementById('editPlanPrice').value;
      if (price < 0) {
        e.preventDefault();
        alert('Price cannot be negative');
      }
      
      const startDate = new Date(document.getElementById('editStartDate').value);
      const nextBillingDate = new Date(document.getElementById('editNextBillingDate').value);
      
      if (nextBillingDate < startDate) {
        e.preventDefault();
        alert('Next billing date cannot be before start date');
      }
    });

    function reloadSubscriptionsTable() {
      const params = new URLSearchParams(window.location.search);
      fetch('../updates/update_subscription.php.php?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.text())
        .then(html => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          const newTable = doc.getElementById('subscriptions-table');
          if (newTable) {
            document.getElementById('subscriptions-table').innerHTML = newTable.innerHTML;
            attachActionHandlers();
          }
        });
    }

    function attachActionHandlers() {
      document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          const subscriptionData = JSON.parse(this.getAttribute('data-subscription'));
          document.getElementById('editSubscriptionId').value = subscriptionData.id;
          document.getElementById('editUsername').value = subscriptionData.username;
          document.getElementById('editPlanName').value = subscriptionData.plan_name;
          document.getElementById('editPlanPrice').value = subscriptionData.plan_price;
          document.getElementById('editBillingCycle').value = subscriptionData.billing_cycle;
          document.getElementById('editPaymentStatus').value = subscriptionData.payment_status.toLowerCase();
          document.getElementById('editCardholderName').value = subscriptionData.cardholder_name;
          document.getElementById('editCardNumber').value = subscriptionData.card_number;
          document.getElementById('editExpiryDate').value = subscriptionData.expiry_date;
          document.getElementById('editTransactionId').value = subscriptionData.transaction_id;
          document.getElementById('editStartDate').value = subscriptionData.start_date.split(' ')[0];
          document.getElementById('editNextBillingDate').value = subscriptionData.next_billing_date.split(' ')[0];
          document.querySelector('#editSubscriptionModal .modal-title').innerHTML = 
            `<i class="bi bi-credit-card me-2"></i> Edit Subscription #${subscriptionData.id}`;
        });
      });

      document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          const subscriptionId = this.getAttribute('data-subscription-id');
          document.getElementById('deleteSubscriptionId').value = subscriptionId;
          document.querySelector('#deleteSubscriptionModal .modal-title').innerHTML = 
            `<i class="bi bi-exclamation-triangle me-2"></i> Delete Subscription #${subscriptionId}`;
        });
      });
    }

    // Initial attach
    attachActionHandlers();

    // AJAX for delete
    document.getElementById('deleteSubscriptionForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch(this.action, {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          var modal = bootstrap.Modal.getInstance(document.getElementById('deleteSubscriptionModal'));
          modal.hide();
          reloadSubscriptionsTable();
        } else {
          alert(data.message);
        }
      });
    });

    // AJAX for edit
    document.getElementById('editSubscriptionForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch(this.action, {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          var modal = bootstrap.Modal.getInstance(document.getElementById('editSubscriptionModal'));
          modal.hide();
          reloadSubscriptionsTable();
        } else {
          alert(data.message);
        }
      });
    });
  </script>
</body>
</html>