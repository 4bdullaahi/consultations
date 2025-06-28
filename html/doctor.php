<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../html/index.php');
    exit;
}
?>
<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>Doctor Dashboard</title>
  
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
  
  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />
  
  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
  
  <!-- Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>
  
  <!-- Custom CSS -->
  <style>
    .summary-card {
      border-radius: 10px;
      padding: 20px;
      color: white;
      margin-bottom: 20px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    
    .summary-card:hover {
      transform: translateY(-5px);
    }
    
    .card-1 { background: linear-gradient(135deg, #3498db, #2c3e50); }
    .card-2 { background: linear-gradient(135deg, #2ecc71, #27ae60); }
    .card-3 { background: linear-gradient(135deg, #e74c3c, #c0392b); }
    .card-4 { background: linear-gradient(135deg, #f39c12, #d35400); }
    
    .chart-container, .table-container, .process-container {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: bold;
    }
    
    .badge-completed { background-color: #d4edda; color: #155724; }
    .badge-scheduled { background-color: #cce5ff; color: #004085; }
    .badge-cancelled { background-color: #f8d7da; color: #721c24; }
    .badge-no-show { background-color: #fff3cd; color: #856404; }
    
    .process-step {
      text-align: center;
      padding: 15px;
    }
    
    .process-step.active {
      font-weight: bold;
      color: #3498db;
    }
    
    .process-step.completed {
      color: #2ecc71;
    }
    
    .process-connector {
      height: 2px;
      background-color: #dee2e6;
      margin: 0 10px;
      position: relative;
      top: -20px;
    }
    
    .process-connector.active {
      background-color: #3498db;
    }
    
    .process-connector.completed {
      background-color: #2ecc71;
    }
  </style>
</head>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->
      <?php require "../lib/docSide.php"; ?>
      <!-- / Menu -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Navbar -->
        <nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
          <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
              <i class="icon-base bx bx-menu icon-md"></i>
            </a>
          </div>

          <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
              <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                  <img src="../assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                </div>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a class="dropdown-item" href="#">
                    <div class="d-flex">
                      <div class="flex-shrink-0 me-3">
                        <div class="avatar avatar-online">
                          <img src="../assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                        </div>
                      </div>
                      <div class="flex-grow-1">
                        <h6 class="mb-0">Dr. Smith</h6>
                        <small class="text-body-secondary">Doctor</small>
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <div class="dropdown-divider my-1"></div>
                </li>
                <li>
                  <a class="dropdown-item" href="#">
                    <i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="#">
                    <i class="icon-base bx bx-cog icon-md me-3"></i><span>Settings</span>
                  </a>
                </li>
                <li>
                  <div class="dropdown-divider my-1"></div>
                </li>
                <li>
                  <a class="dropdown-item" href="javascript:void(0);">
                    <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Log Out</span>
                  </a>
                </li>
              </ul>
            </li>
            <!--/ User -->
          </div>
        </nav>
        <!-- / Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <h4 class="fw-bold py-3 mb-4">Doctor Dashboard</h4>
            
            <!-- Doctor Dashboard Content -->
            <div class="row">
              <!-- Summary Cards -->
              <div class="col-md-3">
                <div class="summary-card card-1">
                  <h5 class="card-title">Total Appointments</h5>
                  <h2 class="card-value" id="total-appointments">0</h2>
                  <p class="card-text"><span id="appointments-change">0%</span> from last month</p>
                </div>
              </div>
              <div class="col-md-3">
                <div class="summary-card card-2">
                  <h5 class="card-title">Completed</h5>
                  <h2 class="card-value" id="completed-appointments">0</h2>
                  <p class="card-text"><span id="completion-rate">0%</span> completion rate</p>
                </div>
              </div>
              <div class="col-md-3">
                <div class="summary-card card-3">
                  <h5 class="card-title">Cancellations</h5>
                  <h2 class="card-value" id="cancelled-appointments">0</h2>
                  <p class="card-text"><span id="cancellation-rate">0%</span> cancellation rate</p>
                </div>
              </div>
              <div class="col-md-3">
                <div class="summary-card card-4">
                  <h5 class="card-title">Revenue</h5>
                  <h2 class="card-value" id="total-revenue">$0</h2>
                  <p class="card-text"><span id="revenue-change">0%</span> from last month</p>
                </div>
              </div>
            </div>
            
            <!-- Charts Row -->
            <div class="row mt-4">
              <div class="col-md-8">
                <div class="chart-container">
                  <h5>Appointments Overview</h5>
                  <canvas id="appointmentsChart" height="250"></canvas>
                </div>
              </div>
              <div class="col-md-4">
                <div class="chart-container">
                  <h5>Payment Status</h5>
                  <canvas id="paymentChart" height="250"></canvas>
                </div>
              </div>
            </div>
            
            <!-- Process Indicator -->
            <div class="row mt-4">
              <div class="col-12">
                <div class="process-container">
                  <h5>Appointment Status Flow</h5>
                  <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="process-step completed">
                      <div class="mb-2"><i class="icon-base bx bx-calendar-check icon-md"></i></div>
                      <div>Scheduled</div>
                      <div class="mt-2" id="scheduled-count">0</div>
                    </div>
                    <div class="process-connector completed" style="flex-grow: 1"></div>
                    <div class="process-step completed">
                      <div class="mb-2"><i class="icon-base bx bx-check-circle icon-md"></i></div>
                      <div>Completed</div>
                      <div class="mt-2" id="completed-count">0</div>
                    </div>
                    <div class="process-connector" style="flex-grow: 1"></div>
                    <div class="process-step">
                      <div class="mb-2"><i class="icon-base bx bx-credit-card icon-md"></i></div>
                      <div>Paid</div>
                      <div class="mt-2" id="paid-count">0</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Recent Appointments Table -->
            <div class="row mt-4">
              <div class="col-12">
                <div class="table-container">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Recent Appointments</h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>Patient ID</th>
                          <th>Date</th>
                          <th>Time</th>
                          <th>Duration</th>
                          <th>Status</th>
                          <th>Payment</th>
                          <th>Amount</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody id="recent-appointments">
                        <!-- Will be populated by JavaScript -->
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
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

  <!-- Core JS -->
  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>
  
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
  
  <!-- Main JS -->
  <script src="../assets/js/main.js"></script>
  
  <!-- Dashboard JS -->
  <script>
    // Initialize charts
    let appointmentsChart, paymentChart;

    document.addEventListener('DOMContentLoaded', function() {
      initCharts();
      fetchDashboardData();
      setInterval(fetchDashboardData, 30000);
    });

    function initCharts() {
      const ctx1 = document.getElementById('appointmentsChart').getContext('2d');
      appointmentsChart = new Chart(ctx1, {
        type: 'line',
        data: {
          labels: [],
          datasets: [
            { label: 'Scheduled', data: [], borderColor: '#3498db', fill: false },
            { label: 'Completed', data: [], borderColor: '#2ecc71', fill: false },
            { label: 'Cancelled', data: [], borderColor: '#e74c3c', fill: false }
          ]
        }
      });

      const ctx2 = document.getElementById('paymentChart').getContext('2d');
      paymentChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
          labels: ['Paid', 'Pending', 'Failed'],
          datasets: [{
            data: [0, 0, 0],
            backgroundColor: ['#2ecc71', '#f39c12', '#e74c3c']
          }]
        }
      });
    }

    function fetchDashboardData() {
      $.ajax({
        url: '../Reports/doctor-dashboard.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
          updateDashboard(data);
        },
        error: function(xhr, status, error) {
          console.error('Error fetching dashboard data:', error);
        }
      });
    }

    function updateDashboard(data) {
      if (!data.summary) return;

      // Update summary cards
      $('#total-appointments').text(data.summary.total_appointments ?? 0);
      $('#completed-appointments').text(data.summary.completed_appointments ?? 0);
      $('#cancelled-appointments').text(data.summary.cancelled_appointments ?? 0);
      $('#total-revenue').text('$' + (data.summary.total_revenue ?? 0));

      // Calculate and update rates/changes
      $('#appointments-change').text((data.summary.appointment_change_percent ?? 0) + '%');
      $('#completion-rate').text((data.summary.completion_rate ?? 0) + '%');
      $('#cancellation-rate').text((data.summary.cancellation_rate ?? 0) + '%');
      $('#revenue-change').text((data.summary.revenue_change_percent ?? 0) + '%');

      // TEMP: Add dummy data if monthlyData is empty
      if (!data.monthlyData || data.monthlyData.length === 0) {
        data.monthlyData = [
          {month: 'Jan', scheduled: 5, completed: 3, cancelled: 2},
          {month: 'Feb', scheduled: 7, completed: 6, cancelled: 1},
          {month: 'Mar', scheduled: 8, completed: 7, cancelled: 1}
        ];
      }
      if (!data.paymentData || Object.keys(data.paymentData).length === 0) {
        data.paymentData = {paid: 10, pending: 3, failed: 1};
      }

      updateAppointmentsChart(data.monthlyData);
      updatePaymentChart(data.paymentData);

      // Update process flow
      $('#scheduled-count').text(data.flow?.scheduled ?? 0);
      $('#completed-count').text(data.flow?.completed ?? 0);
      $('#paid-count').text(data.flow?.paid ?? 0);

      // Update recent appointments table
      updateRecentAppointments(data.recentAppointments || []);
    }

    function updateAppointmentsChart(monthlyData) {
      if (!appointmentsChart) return;
      appointmentsChart.data.labels = monthlyData.map(item => item.month);
      appointmentsChart.data.datasets[0].data = monthlyData.map(item => item.scheduled ?? 0);
      appointmentsChart.data.datasets[1].data = monthlyData.map(item => item.completed ?? 0);
      appointmentsChart.data.datasets[2].data = monthlyData.map(item => item.cancelled ?? 0);
      appointmentsChart.update();
    }

    function updatePaymentChart(paymentData) {
      if (!paymentChart) return;
      paymentChart.data.datasets[0].data = [
        paymentData.paid ?? 0,
        paymentData.pending ?? 0,
        paymentData.failed ?? 0
      ];
      paymentChart.update();
    }

    function updateRecentAppointments(appointments) {
      let tableHtml = '';
      appointments.forEach(appointment => {
        tableHtml += `
          <tr>
            <td>${appointment.patient_id ?? ''}</td>
            <td>${appointment.patient_name ?? 'N/A'}</td>
            <td>${appointment.appointment_date ?? ''}</td>
            <td>${formatTime(appointment.appointment_time ?? '')}</td>
            <td>${appointment.duration ?? ''} min</td>
            <td><span class="status-badge badge-${getStatusClass(appointment.status)}">${appointment.status}</span></td>
            <td><span class="status-badge badge-${getStatusClass(appointment.payment_status)}">${appointment.payment_status}</span></td>
            <td>$${appointment.amount ? Number(appointment.amount).toFixed(2) : '0.00'}</td>
            <td>
              <button class="btn btn-sm btn-outline-secondary">Details</button>
              ${appointment.link ? `<a href="${appointment.link}" class="btn btn-sm btn-primary ms-1">Join</a>` : ''}
            </td>
          </tr>
        `;
      });
      $('#recent-appointments').html(tableHtml);
    }
    
    function formatTime(timeString) {
      const [hours, minutes] = timeString.split(':');
      const hour = parseInt(hours);
      const ampm = hour >= 12 ? 'PM' : 'AM';
      const displayHour = hour % 12 || 12;
      return `${displayHour}:${minutes} ${ampm}`;
    }
    
    function getStatusClass(status) {
      const statusMap = {
        'completed': 'completed',
        'scheduled': 'scheduled',
        'cancelled': 'cancelled',
        'no-show': 'no-show',
        'paid': 'completed',
        'pending': 'scheduled',
        'failed': 'cancelled'
      };
      return statusMap[status.toLowerCase()] || '';
    }
  </script>
</body>
</html>