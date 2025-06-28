<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}
if ($_SESSION['role'] !== 'patient') {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['user_id'];
?>
<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Patient Dashboard | Medical System</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

  <!-- Page CSS -->
  <style>
    :root {
      --primary: #2a7fba;
      --secondary: #3bb77e;
      --danger: #e74c3c;
      --warning: #f39c12;
      --light-bg: #f8fafc;
    }
    
    .patient-dashboard {
      padding: 20px;
      max-width: 1400px;
      margin: 0 auto;
    }
    
    .welcome-card {
      background: linear-gradient(135deg, var(--primary) 0%, #4a90e2 100%);
      color: white;
      border-radius: 10px;
      padding: 25px;
      margin-bottom: 25px;
      box-shadow: 0 4px 20px rgba(42, 127, 186, 0.15);
    }
    
    .card-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
      margin-bottom: 25px;
    }
    
    .summary-card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      display: flex;
      align-items: center;
      border-left: 4px solid var(--primary);
      transition: transform 0.2s;
    }
    
    .summary-card:hover {
      transform: translateY(-3px);
    }
    
    .card-icon {
      font-size: 24px;
      color: var(--primary);
      margin-right: 15px;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background-color: rgba(42, 127, 186, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .card-content h3 {
      margin: 0 0 5px 0;
      font-size: 16px;
      color: #666;
      font-weight: 500;
    }
    
    .count {
      font-size: 28px;
      font-weight: 700;
      margin: 0;
      color: #333;
    }
    
    .trend {
      margin: 5px 0 0 0;
      font-size: 12px;
      display: flex;
      align-items: center;
    }
    
    .trend.up {
      color: var(--secondary);
    }
    
    .trend.down {
      color: var(--danger);
    }
    
    .trend i {
      margin-right: 4px;
    }
    
    .chart-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 25px;
    }
    
    @media (max-width: 768px) {
      .chart-row {
        grid-template-columns: 1fr;
      }
    }
    
    .chart-container {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    
    .chart-container h3 {
      margin-top: 0;
      margin-bottom: 15px;
      font-size: 16px;
      color: #444;
      font-weight: 600;
    }
    
    .table-container {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.05);
      overflow-x: auto;
    }
    
    .table-container h3 {
      margin-top: 0;
      margin-bottom: 15px;
      font-size: 16px;
      color: #444;
      font-weight: 600;
    }
    
    .data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }
    
    .data-table th {
      text-align: left;
      padding: 12px 15px;
      background: #f8fafc;
      font-weight: 600;
      color: #555;
    }
    
    .data-table td {
      padding: 12px 15px;
      border-bottom: 1px solid #eee;
    }
    
    .status-badge {
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 500;
      display: inline-block;
    }
    
    .status-active {
      background: #e8f8f0;
      color: var(--secondary);
    }
    
    .status-pending {
      background: #fff4e5;
      color: var(--warning);
    }
    
    .status-cancelled {
      background: #fee;
      color: var(--danger);
    }
    
    .action-btn {
      padding: 5px 10px;
      border-radius: 5px;
      font-size: 12px;
      cursor: pointer;
      border: none;
      transition: all 0.3s;
    }
    
    .btn-primary {
      background-color: var(--primary);
      color: white;
    }
    
    .btn-primary:hover {
      background-color: #1e6ea7;
    }
    
    .loading {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(0,0,0,.1);
      border-radius: 50%;
      border-top-color: var(--primary);
      animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
  </style>

  <!-- Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>
</head>
<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->
      <?php include("../lib/pSidebar.php"); ?>
      <!-- / Menu -->
      <div class="layout-page">
        <!-- Content wrapper -->
        <div class="content-wrapper main-content">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="patient-dashboard">
              <!-- Welcome Card -->
              <div class="welcome-card">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h3 class="text-white mb-1">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
                    <p class="text-white mb-0">Here's your health dashboard</p>
                  </div>
                  <button class="btn btn-outline-light">View Profile</button>
                </div>
              </div>

              <!-- Row 1: Summary Cards -->
              <div class="card-row">
                <!-- Card 1: Upcoming Appointments -->
                <div class="summary-card">
                  <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
                  <div class="card-content">
                    <h3>Upcoming Appointments</h3>
                    <p class="count" id="upcoming-appointments">--</p>
                    <a href="appointments.php" class="text-primary small">View all</a>
                  </div>
                </div>
                
                <!-- Card 2: Total Appointments -->
                <div class="summary-card">
                  <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
                  <div class="card-content">
                    <h3>Total Appointments</h3>
                    <p class="count" id="total-appointments">--</p>
                    <a href="appointments.php" class="text-primary small">View history</a>
                  </div>
                </div>
                
                <!-- Card 3: Active Subscription -->
                <div class="summary-card">
                  <div class="card-icon"><i class="fas fa-id-card"></i></div>
                  <div class="card-content">
                    <h3>Subscription Plan</h3>
                    <p class="count" id="subscription-plan">--</p>
                    <p class="trend" id="subscription-status"><span class="status-badge">Loading...</span></p>
                  </div>
                </div>
                
                <!-- Card 4: Total Spending -->
                <div class="summary-card">
                  <div class="card-icon"><i class="fas fa-money-bill-wave"></i></div>
                  <div class="card-content">
                    <h3>Total Spending</h3>
                    <p class="count" id="total-spending">--</p>
                    <a href="payments.php" class="text-primary small">View details</a>
                  </div>
                </div>
              </div>

              <!-- Row 2: Charts -->
              <div class="chart-row">
                <!-- Chart 1: Appointment Status Distribution -->
                <div class="chart-container">
                  <h3>Appointment Status</h3>
                  <canvas id="appointmentStatusChart"></canvas>
                </div>
                
                <!-- Chart 2: Appointment Trend -->
                <div class="chart-container">
                  <h3>Appointment Trend (Last 30 Days)</h3>
                  <canvas id="appointmentTrendChart"></canvas>
                </div>
              </div>

              <!-- Row 3: Recent Appointments Table -->
              <div class="table-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h3 class="mb-0">Recent Appointments</h3>
                  <button class="btn btn-sm btn-primary" id="new-appointment-btn">
                    <i class="fas fa-plus me-1"></i> New Appointment
                  </button>
                </div>
                <table class="data-table" id="appointments-table">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Time</th>
                      <th>Doctor</th>
                      <th>Status</th>
                      <th>Payment</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="6" style="text-align: center;">
                        <div class="loading"></div> Loading appointments...
                      </td>
                    </tr>
                  </tbody>
                </table>
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
  <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../assets/js/main.js"></script>
  
  <script>
    // Global variables to store chart instances
    let appointmentStatusChart, appointmentTrendChart;

    // Function to fetch patient dashboard data
    async function fetchPatientDashboardData() {
      try {
        const response = await fetch(`../Reports/patient_dashboard.php`);
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return await response.json();
      } catch (error) {
        console.error('Error fetching patient dashboard data:', error);
        return null;
      }
    }

    // Function to update summary cards
    function updateSummaryCards(data) {
      if (!data) return;

      // Summary statistics
      if (data.summary_stats) {
        document.getElementById('upcoming-appointments').textContent = data.summary_stats.upcoming_appointments || '0';
        document.getElementById('total-appointments').textContent = data.summary_stats.total_appointments || '0';
        document.getElementById('total-spending').textContent = '$' + (parseFloat(data.summary_stats.total_paid) || 0).toLocaleString('en-US', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
      }

      // Subscription info
      if (data.subscription) {
        const subPlan = document.getElementById('subscription-plan');
        const subStatus = document.getElementById('subscription-status');
        
        if (data.subscription.plan_name) {
          subPlan.textContent = data.subscription.plan_name;
          subStatus.innerHTML = data.subscription.payment_status === 'active' ? 
            '<span class="status-badge status-active">Active</span>' : 
            '<span class="status-badge status-cancelled">Inactive</span>';
        } else {
          subPlan.textContent = 'No Subscription';
          subStatus.innerHTML = '<span class="status-badge status-cancelled">None</span>';
        }
      }
    }

    // Function to update appointment status chart
    function updateAppointmentStatusChart(data) {
      if (!data || !data.appointment_status) return;

      const ctx = document.getElementById('appointmentStatusChart').getContext('2d');
      
      // Process data for chart
      const labels = data.appointment_status.map(item => item.status);
      const counts = data.appointment_status.map(item => item.count);
      
      const backgroundColors = labels.map(status => {
        switch(status.toLowerCase()) {
          case 'completed': return '#3bb77e';
          case 'scheduled': return '#2a7fba';
          case 'cancelled': return '#e74c3c';
          default: return '#f39c12';
        }
      });

      // Destroy previous chart if it exists
      if (appointmentStatusChart) {
        appointmentStatusChart.destroy();
      }

      // Create new chart
      appointmentStatusChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: counts,
            backgroundColor: backgroundColors,
            borderWidth: 0
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                usePointStyle: true,
                padding: 20
              }
            }
          }
        }
      });
    }

    // Function to update appointment trend chart
    function updateAppointmentTrendChart(data) {
      if (!data || !data.appointment_trend) return;

      const ctx = document.getElementById('appointmentTrendChart').getContext('2d');
      
      // Process data for chart
      const labels = data.appointment_trend.map(item => new Date(item.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
      const counts = data.appointment_trend.map(item => item.appointment_count);

      // Destroy previous chart if it exists
      if (appointmentTrendChart) {
        appointmentTrendChart.destroy();
      }

      // Create new chart
      appointmentTrendChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Appointments',
            data: counts,
            borderColor: '#2a7fba',
            backgroundColor: 'rgba(42, 127, 186, 0.1)',
            tension: 0.3,
            fill: true,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#2a7fba',
            pointBorderWidth: 2
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                drawBorder: false
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });
    }

    // Function to update appointments table
    function updateAppointmentsTable(data) {
      if (!data || !data.recent_appointments) return;

      const tbody = document.querySelector('#appointments-table tbody');
      tbody.innerHTML = '';

      if (data.recent_appointments.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="6" style="text-align: center;">
              No appointments found. <a href="../pages/appointments.php" class="text-primary">Schedule one now</a>
            </td>
          </tr>
        `;
        return;
      }

      data.recent_appointments.forEach(appt => {
        const row = document.createElement('tr');
        
        // Date
        const dateCell = document.createElement('td');
        dateCell.textContent = new Date(appt.appointment_date).toLocaleDateString('en-US', {
          month: 'short',
          day: 'numeric',
          year: 'numeric'
        });
        row.appendChild(dateCell);
        
        // Time
        const timeCell = document.createElement('td');
        timeCell.textContent = appt.appointment_time || '--';
        row.appendChild(timeCell);
        
        // Doctor
        const doctorCell = document.createElement('td');
        doctorCell.textContent = appt.doctor_name || '--';
        row.appendChild(doctorCell);
        
        // Status
        const statusCell = document.createElement('td');
        const statusBadge = document.createElement('span');
        statusBadge.className = 'status-badge';
        
        switch(appt.status.toLowerCase()) {
          case 'completed':
            statusBadge.classList.add('status-active');
            break;
          case 'scheduled':
            statusBadge.classList.add('status-pending');
            break;
          case 'cancelled':
            statusBadge.classList.add('status-cancelled');
            break;
        }
        statusBadge.textContent = appt.status;
        statusCell.appendChild(statusBadge);
        row.appendChild(statusCell);
        
        // Payment Status
        const paymentCell = document.createElement('td');
        paymentCell.textContent = appt.payment_status || '--';
        row.appendChild(paymentCell);
        
        // Amount
        const amountCell = document.createElement('td');
        amountCell.textContent = appt.amount ? '$' + parseFloat(appt.amount).toFixed(2) : '--';
        row.appendChild(amountCell);
        
        tbody.appendChild(row);
      });
    }

    // Main function to load and display all dashboard data
    async function loadPatientDashboardData() {
      const data = await fetchPatientDashboardData();
      
      if (data) {
        console.log('Patient dashboard data:', data);
        updateSummaryCards(data);
        updateAppointmentStatusChart(data);
        updateAppointmentTrendChart(data);
        updateAppointmentsTable(data);
      } else {
        // Handle error case
        console.error('Failed to load patient dashboard data');
        alert('Failed to load dashboard data. Please try again later.');
      }
    }

    // Initialize dashboard when page loads
    document.addEventListener('DOMContentLoaded', function() {
      loadPatientDashboardData();
      
      // Event listener for new appointment button
      document.getElementById('new-appointment-btn').addEventListener('click', function() {
        window.location.href = '../pages/appointments.php';
      });

      // Refresh data every 5 minutes
      setInterval(loadPatientDashboardData, 5 * 60 * 1000);
    });
  </script>
</body>
</html>