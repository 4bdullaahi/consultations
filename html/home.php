<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Admin Dashboard | Medical System</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

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
    
    .medical-dashboard {
      padding: 20px;
      max-width: 1400px;
      margin: 0 auto;
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
      <?php include("../lib/sidebar.php"); ?>
      <!-- / Menu -->
      <div class="layout-page">
        <!-- Content wrapper -->
        <div class="content-wrapper main-content">
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="medical-dashboard">
              <!-- Row 1: Summary Cards -->
              <div class="card-row">
                <!-- Card 1: Total Users -->
                <div class="summary-card">
                  <div class="card-icon"><i class="fas fa-users"></i></div>
                  <div class="card-content">
                    <h3>Total Users</h3>
                    <p class="count" id="total-users">--</p>
                    <p class="trend" id="user-trend"><i class="fas fa-arrow-up"></i> <span class="trend-text">Loading...</span></p>
                  </div>
                </div>
                
                <!-- Card 2: Today's Appointments -->
                <div class="summary-card">
                  <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
                  <div class="card-content">
                    <h3>Today's Appointments</h3>
                    <p class="count" id="today-appointments">--</p>
                    <p class="trend" id="appointment-trend"><i class="fas fa-arrow-up"></i> <span class="trend-text">Loading...</span></p>
                  </div>
                </div>
                
                <!-- Card 3: Active Doctors -->
                <div class="summary-card">
                  <div class="card-icon"><i class="fas fa-user-md"></i></div>
                  <div class="card-content">
                    <h3>Active Doctors</h3>
                    <p class="count" id="total-doctors">--</p>
                    <p class="trend" id="doctor-trend"><i class="fas fa-arrow-up"></i> <span class="trend-text">Loading...</span></p>
                  </div>
                </div>
                
                <!-- Card 4: Today's Payments -->
                <div class="summary-card">
                  <div class="card-icon"><i class="fas fa-money-bill-wave"></i></div>
                  <div class="card-content">
                    <h3>Today's Payments</h3>
                    <p class="count" id="today-revenue">--</p>
                    <p class="trend" id="revenue-trend"><i class="fas fa-arrow-up"></i> <span class="trend-text">Loading...</span></p>
                  </div>
                </div>
              </div>

              <!-- Row 2: Charts -->
              <div class="chart-row">
                <!-- Chart 1: Appointments Distribution -->
                <div class="chart-container">
                  <h3>Appointments Distribution</h3>
                  <canvas id="appointmentsPieChart"></canvas>
                </div>
                
                <!-- Chart 2: Revenue Trend -->
                <div class="chart-container">
                  <h3>Revenue Trend (Last 30 Days)</h3>
                  <canvas id="revenueLineChart"></canvas>
                </div>
              </div>

              <!-- Row 3: Data Table -->
              <div class="table-container">
                <h3>Recent Payments</h3>
                <table class="data-table" id="payments-table">
                  <thead>
                    <tr>
                      <th>User</th>
                      <th>Description</th>
                      <th>Amount</th>
                      <th>Date</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="5" style="text-align: center;">
                        <div class="loading"></div> Loading data...
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
    let pieChart, lineChart;

    // Function to fetch dashboard data
    async function fetchDashboardData() {
      try {
        const response = await fetch('../Reports/adminDashbaordAPI.php');
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return await response.json();
      } catch (error) {
        console.error('Error fetching dashboard data:', error);
        return null;
      }
    }

    // Function to update summary cards
    function updateSummaryCards(data) {
      if (!data) return;

      // User stats
      const userStats = data.user_stats;
      if (userStats) {
        document.getElementById('total-users').textContent = userStats.total_users || '0';
        
        const newUsersToday = parseInt(userStats.new_users_today) || 0;
        const newUsersYesterday = parseInt(userStats.new_users_yesterday) || 0;
        const userTrendElement = document.getElementById('user-trend');
        const userTrendText = userTrendElement.querySelector('.trend-text');
        
        if (newUsersYesterday > 0) {
          const percentChange = ((newUsersToday - newUsersYesterday) / newUsersYesterday * 100).toFixed(1);
          userTrendText.textContent = `${Math.abs(percentChange)}% ${newUsersToday >= newUsersYesterday ? 'increase' : 'decrease'} from yesterday`;
          
          if (newUsersToday >= newUsersYesterday) {
            userTrendElement.classList.add('up');
            userTrendElement.classList.remove('down');
            userTrendElement.querySelector('i').className = 'fas fa-arrow-up';
          } else {
            userTrendElement.classList.add('down');
            userTrendElement.classList.remove('up');
            userTrendElement.querySelector('i').className = 'fas fa-arrow-down';
          }
        } else {
          userTrendText.textContent = `${newUsersToday} new today`;
          userTrendElement.classList.add('up');
          userTrendElement.querySelector('i').className = 'fas fa-arrow-up';
        }
      }

      // Doctor stats
      const doctorStats = data.doctor_stats;
      if (doctorStats) {
        document.getElementById('total-doctors').textContent = doctorStats.total_doctors || '0';
        
        const newDoctorsToday = parseInt(doctorStats.new_doctors_today) || 0;
        const doctorTrendElement = document.getElementById('doctor-trend');
        const doctorTrendText = doctorTrendElement.querySelector('.trend-text');
        
        if (newDoctorsToday > 0) {
          doctorTrendText.textContent = `${newDoctorsToday} new today`;
          doctorTrendElement.classList.add('up');
          doctorTrendElement.querySelector('i').className = 'fas fa-arrow-up';
        } else {
          doctorTrendText.textContent = 'No new doctors today';
          doctorTrendElement.classList.remove('up', 'down');
          doctorTrendElement.querySelector('i').className = 'fas fa-minus';
        }
      }

      // Appointment stats
      const appointmentStats = data.appointment_stats;
      if (appointmentStats) {
        document.getElementById('today-appointments').textContent = appointmentStats.today_appointments || '0';
        
        const todayApps = parseInt(appointmentStats.today_appointments) || 0;
        const yesterdayApps = parseInt(appointmentStats.yesterday_appointments) || 0;
        const appointmentTrendElement = document.getElementById('appointment-trend');
        const appointmentTrendText = appointmentTrendElement.querySelector('.trend-text');
        
        if (yesterdayApps > 0) {
          const percentChange = ((todayApps - yesterdayApps) / yesterdayApps * 100).toFixed(1);
          appointmentTrendText.textContent = `${Math.abs(percentChange)}% ${todayApps >= yesterdayApps ? 'increase' : 'decrease'} from yesterday`;
          
          if (todayApps >= yesterdayApps) {
            appointmentTrendElement.classList.add('up');
            appointmentTrendElement.classList.remove('down');
            appointmentTrendElement.querySelector('i').className = 'fas fa-arrow-up';
          } else {
            appointmentTrendElement.classList.add('down');
            appointmentTrendElement.classList.remove('up');
            appointmentTrendElement.querySelector('i').className = 'fas fa-arrow-down';
          }
        } else {
          appointmentTrendText.textContent = `${todayApps} today`;
          if (todayApps > 0) {
            appointmentTrendElement.classList.add('up');
            appointmentTrendElement.classList.remove('down');
            appointmentTrendElement.querySelector('i').className = 'fas fa-arrow-up';
          } else {
            appointmentTrendElement.classList.remove('up', 'down');
            appointmentTrendElement.querySelector('i').className = 'fas fa-minus';
          }
        }
      }

      // Revenue stats
      if (appointmentStats) {
        const todayRevenue = parseFloat(appointmentStats.today_revenue) || 0;
        const yesterdayRevenue = parseFloat(appointmentStats.yesterday_revenue) || 0;
        
        document.getElementById('today-revenue').textContent = `$${todayRevenue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        
        const revenueTrendElement = document.getElementById('revenue-trend');
        const revenueTrendText = revenueTrendElement.querySelector('.trend-text');
        
        if (yesterdayRevenue > 0) {
          const percentChange = ((todayRevenue - yesterdayRevenue) / yesterdayRevenue * 100).toFixed(1);
          revenueTrendText.textContent = `${Math.abs(percentChange)}% ${todayRevenue >= yesterdayRevenue ? 'increase' : 'decrease'} from yesterday`;
          
          if (todayRevenue >= yesterdayRevenue) {
            revenueTrendElement.classList.add('up');
            revenueTrendElement.classList.remove('down');
            revenueTrendElement.querySelector('i').className = 'fas fa-arrow-up';
          } else {
            revenueTrendElement.classList.add('down');
            revenueTrendElement.classList.remove('up');
            revenueTrendElement.querySelector('i').className = 'fas fa-arrow-down';
          }
        } else {
          revenueTrendText.textContent = todayRevenue > 0 ? '$' + todayRevenue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' today' : 'No revenue today';
          if (todayRevenue > 0) {
            revenueTrendElement.classList.add('up');
            revenueTrendElement.classList.remove('down');
            revenueTrendElement.querySelector('i').className = 'fas fa-arrow-up';
          } else {
            revenueTrendElement.classList.remove('up', 'down');
            revenueTrendElement.querySelector('i').className = 'fas fa-minus';
          }
        }
      }
    }

    // Function to update appointments pie chart
    function updateAppointmentsPieChart(data) {
      if (!data || !data.appointment_distribution) return;

      const ctx = document.getElementById('appointmentsPieChart').getContext('2d');
      
      // Process data for chart
      const labels = [];
      const counts = [];
      const backgroundColors = [];
      
      data.appointment_distribution.forEach(item => {
        labels.push(item.status.charAt(0).toUpperCase() + item.status.slice(1));
        counts.push(parseInt(item.count) || 0);
        
        // Assign colors based on status
        switch(item.status.toLowerCase()) {
          case 'completed':
            backgroundColors.push('#3bb77e');
            break;
          case 'scheduled':
            backgroundColors.push('#2a7fba');
            break;
          case 'cancelled':
            backgroundColors.push('#e74c3c');
            break;
          default:
            backgroundColors.push('#f39c12');
        }
      });

      // Destroy previous chart if it exists
      if (pieChart) {
        pieChart.destroy();
      }

      // Create new chart
      pieChart = new Chart(ctx, {
        type: 'pie',
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
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.raw || 0;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = Math.round((value / total) * 100);
                  return `${label}: ${value} (${percentage}%)`;
                }
              }
            }
          }
        }
      });
    }

    // Function to update revenue line chart
    function updateRevenueLineChart(data) {
      if (!data || !data.revenue_trend) return;

      const ctx = document.getElementById('revenueLineChart').getContext('2d');
      
      // Process data for chart
      const labels = [];
      const revenueData = [];
      
      data.revenue_trend.forEach(item => {
        const date = new Date(item.date);
        labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
        revenueData.push(parseFloat(item.daily_revenue) || 0);
      });

      // Destroy previous chart if it exists
      if (lineChart) {
        lineChart.destroy();
      }

      // Create new chart
      lineChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Daily Revenue',
            data: revenueData,
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
            },
            tooltip: {
              mode: 'index',
              intersect: false,
              callbacks: {
                label: function(context) {
                  return `$${context.raw.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: false,
              grid: {
                drawBorder: false
              },
              ticks: {
                callback: function(value) {
                  return '$' + value.toLocaleString('en-US');
                }
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

    // Function to update recent payments table
    function updateRecentPaymentsTable(data) {
      if (!data || !data.recent_payments) return;

      const tableBody = document.querySelector('#payments-table tbody');
      tableBody.innerHTML = '';

      if (data.recent_payments.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No recent payments found</td></tr>';
        return;
      }

      data.recent_payments.forEach(payment => {
        const row = document.createElement('tr');
        
        // User column
        const userCell = document.createElement('td');
        userCell.textContent = payment.username || payment.user || 'N/A';
        row.appendChild(userCell);
        
        // Description column
        const descCell = document.createElement('td');
        descCell.textContent = payment.description || 'N/A';
        row.appendChild(descCell);
        
        // Amount column
        const amountCell = document.createElement('td');
        amountCell.textContent = '$' + (parseFloat(payment.amount) || 0).toLocaleString('en-US', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
        row.appendChild(amountCell);
        
        // Date column
        const dateCell = document.createElement('td');
        const paymentDate = new Date(payment.payment_date || payment.created_at || payment.date);
        dateCell.textContent = paymentDate.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
        row.appendChild(dateCell);
        
        // Status column
        const statusCell = document.createElement('td');
        const statusBadge = document.createElement('span');
        statusBadge.className = 'status-badge';
        
        const paymentStatus = (payment.payment_status || payment.status || '').toLowerCase();
        if (paymentStatus.includes('active') || paymentStatus.includes('paid') || paymentStatus.includes('completed')) {
          statusBadge.classList.add('status-active');
          statusBadge.textContent = 'Active';
        } else if (paymentStatus.includes('pending')) {
          statusBadge.classList.add('status-pending');
          statusBadge.textContent = 'Pending';
        } else if (paymentStatus.includes('cancel')) {
          statusBadge.classList.add('status-cancelled');
          statusBadge.textContent = 'Cancelled';
        } else {
          statusBadge.textContent = paymentStatus.charAt(0).toUpperCase() + paymentStatus.slice(1);
        }
        
        statusCell.appendChild(statusBadge);
        row.appendChild(statusCell);
        
        tableBody.appendChild(row);
      });
    }

    // Main function to load and display all dashboard data
    async function loadDashboardData() {
      const data = await fetchDashboardData();
      
      if (data) {
        console.log('Dashboard API data:', data);
        updateSummaryCards(data);
        updateAppointmentsPieChart(data);
        updateRevenueLineChart(data);
        updateRecentPaymentsTable(data);
      } else {
        // Handle error case
        console.error('Failed to load dashboard data');
        alert('Failed to load dashboard data. Please try again later.');
      }
    }

    // Initialize dashboard when page loads
    document.addEventListener('DOMContentLoaded', function() {
      loadDashboardData();
      
      // Refresh data every 5 minutes
      setInterval(loadDashboardData, 5 * 60 * 1000);
    });
  </script>
</body>
</html>