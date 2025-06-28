<?php

session_start();
$patient_id = $_SESSION['user_id'] ?? null;
if (!$patient_id) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "User not authenticated. Please log in."]);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthcare Appointment Booking</title>
    
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
    <!-- Your custom style to remove underline -->
    <style>
      .layout-menu a,
      .menu-vertical a,
      .menu-link {
        text-decoration: none !important;
      }
    </style>
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #7367f0;
            --primary-hover: #5d52d1;
            --secondary-color: #82868b;
            --success-color: #28c76f;
            --info-color: #00cfe8;
            --warning-color: #ff9f43;
            --danger-color: #ea5455;
            --light-color: #f6f6f6;
            --dark-color: #4b4b4b;
            --border-radius: 0.5rem;
            --box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
            --transition: all 0.3s ease-in-out;
        }

        body {
            background-color: #f8f8f8;
            font-family: 'Public Sans', sans-serif;
            color: #6e6b7b;
        }

        .booking-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 15px;
        }

        .booking-card {
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            transition: var(--transition);
        }

        .booking-header {
            padding: 1.5rem;
            background: linear-gradient(118deg, var(--primary-color), rgba(115, 103, 240, 0.7));
            color: white;
            text-align: center;
        }

        .booking-header h2 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .booking-body {
            padding: 2rem;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #ebe9f1;
            z-index: 1;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #ebe9f1;
            color: #6e6b7b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 0.5rem;
            transition: var(--transition);
        }

        .step.active .step-number {
            background-color: var(--primary-color);
            color: white;
        }

        .step.completed .step-number {
            background-color: var(--success-color);
            color: white;
        }

        .step-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #b9b9c3;
        }

        .step.active .step-label,
        .step.completed .step-label {
            color: var(--dark-color);
        }

        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .doctor-card {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
            border: 1px solid #ebe9f1;
            background-color: white;
            transition: var(--transition);
            cursor: pointer;
        }

        .doctor-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px -8px rgba(115, 103, 240, 0.4);
        }

        .doctor-card.selected {
            border-color: var(--primary-color);
            background-color: rgba(115, 103, 240, 0.05);
        }

        .doctor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1.5rem;
            border: 3px solid #ebe9f1;
        }

        .doctor-info {
            flex: 1;
        }

        .doctor-name {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }

        .doctor-specialty {
            color: var(--secondary-color);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .doctor-rating {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .doctor-price {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.25rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            border: 1px solid #d8d6de;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 3px 10px 0 rgba(115, 103, 240, 0.15);
        }

        .summary-card {
            background-color: #f8f8f8;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .summary-label {
            color: var(--secondary-color);
        }

        .summary-value {
            font-weight: 500;
            color: var(--dark-color);
        }

        .summary-total {
            border-top: 1px solid #ebe9f1;
            padding-top: 1rem;
            margin-top: 1rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .btn {
            border-radius: var(--border-radius);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .btn-outline-secondary {
            border-color: #d8d6de;
            color: #6e6b7b;
        }

        .btn-outline-secondary:hover {
            background-color: #f8f8f8;
            color: var(--dark-color);
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #ebe9f1;
        }

        .search-box {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-box .form-control {
            padding-left: 2.5rem;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
        }

        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .time-slot {
            padding: 0.5rem;
            text-align: center;
            border-radius: var(--border-radius);
            border: 1px solid #ebe9f1;
            background-color: white;
            cursor: pointer;
            transition: var(--transition);
        }

        .time-slot:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .time-slot.selected {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .time-slot.unavailable {
            background-color: #f8f8f8;
            color: #b9b9c3;
            cursor: not-allowed;
            text-decoration: line-through;
        }

        .payment-methods {
            margin-top: 1.5rem;
        }

        .payment-card {
            border: 1px solid #ebe9f1;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .payment-card:hover {
            border-color: var(--primary-color);
        }

        .payment-card.selected {
            border-color: var(--primary-color);
            background-color: rgba(115, 103, 240, 0.05);
        }

        .payment-icon {
            font-size: 2rem;
            margin-right: 1rem;
            color: var(--secondary-color);
        }

        .confirmation-icon {
            font-size: 5rem;
            color: var(--success-color);
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .doctor-card {
                flex-direction: column;
                text-align: center;
            }
            
            .doctor-avatar {
                margin-right: 0;
                margin-bottom: 1rem;
            }
            
            .time-slots {
                grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            }
        }
    </style>
</head>

<body>
   <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <?php require_once("../lib/pSidebar.php"); ?>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">

    <div class="booking-container">
        <div class="booking-card">
            <div class="booking-header">
                <h2>Book an Appointment</h2>
                <p>Schedule your visit with our healthcare professionals</p>
            </div>
            
            <div class="booking-body">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active" id="step1-indicator">
                        <div class="step-number">1</div>
                        <div class="step-label">Select Doctor</div>
                    </div>
                    <div class="step" id="step2-indicator">
                        <div class="step-number">2</div>
                        <div class="step-label">Date & Time</div>
                    </div>
                    <div class="step" id="step3-indicator">
                        <div class="step-number">3</div>
                        <div class="step-label">Payment</div>
                    </div>
                    <div class="step" id="step4-indicator">
                        <div class="step-number">4</div>
                        <div class="step-label">Confirmation</div>
                    </div>
                </div>
                
                <!-- Step 1: Select Doctor -->
                <div class="step-content active" id="step1-content">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" id="doctorSearch" placeholder="Search for doctors...">
                    </div>
                    
                    <div id="doctorList">
                        <!-- Doctor cards will be loaded here -->
                        <!-- Load doctors from backend -->
                        <script>
                          // Load doctors from backend
                          fetch('../lib/api.php?action=get_doctors')
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      const doctorList = document.getElementById('doctorList');
      doctorList.innerHTML = '';
      data.doctors.forEach(doc => {
        const card = document.createElement('div');
        card.className = 'doctor-card';
        card.dataset.id = doc.id;
        card.dataset.doctor = doc.username; // Use username from DB
        card.dataset.specialty = doc.sepcialization; // Use sepcialization from DB
        card.dataset.price = doc.price;
        card.innerHTML = `
          <img src='../uploads/${doc.img} alt="${doc.username}' class="doctor-avatar" onerror="this.onerror=null;this.src='../uploads/';">
          <div class="doctor-info">
            <h5 class="doctor-name">${doc.username}</h5>
            <p class="doctor-specialty">${doc.sepcialization}</p>
            <div class="doctor-rating"><span class="text-warning">★★★★★</span></div>
          </div>
          <div class="doctor-price">$${doc.price}</div>
        `;
        doctorList.appendChild(card);
      });

      // Attach doctor selection event
      document.querySelectorAll('.doctor-card').forEach(card => {
        card.addEventListener('click', function() {
          document.querySelectorAll('.doctor-card').forEach(c => c.classList.remove('selected'));
          this.classList.add('selected');
          selectedDoctor = {
            id: this.dataset.id,
            name: this.dataset.doctor,
            specialty: this.dataset.specialty,
            price: this.dataset.price,
            image: this.querySelector('.doctor-avatar').src
          };
        });
      });
    }
  });
                        </script>
                        <!-- Hardcoded doctor cards for fallback -->
                        <div class="doctor-card" data-id="1" data-doctor="Dr. John Doe" data-specialty="Cardiologist" data-price="150">
                            <img src="../uploads/default.png" alt="Dr. John Doe" class="doctor-avatar">
                            <div class="doctor-info">
                                <h5 class="doctor-name">Dr. John Doe</h5>
                                <p class="doctor-specialty">Cardiologist</p>
                                <div class="doctor-rating"><span class="text-warning">★★★★★</span></div>
                            </div>
                            <div class="doctor-price">$150</div>
                        </div>
                        <div class="doctor-card" data-id="2" data-doctor="Dr. Jane Smith" data-specialty="Dermatologist" data-price="120">
                            <img src="../uploads/default.png" alt="Dr. Jane Smith" class="doctor-avatar">
                            <div class="doctor-info">
                                <h5 class="doctor-name">Dr. Jane Smith</h5>
                                <p class="doctor-specialty">Dermatologist</p>
                                <div class="doctor-rating"><span class="text-warning">★★★★★</span></div>
                            </div>
                            <div class="doctor-price">$120</div>
                        </div>
                        <div class="doctor-card" data-id="3" data-doctor="Dr. Emily White" data-specialty="Pediatrician" data-price="130">
                            <img src="../uploads/default.png" alt="Dr. Emily White" class="doctor-avatar">
                            <div class="doctor-info">
                                <h5 class="doctor-name">Dr. Emily White</h5>
                                <p class="doctor-specialty">Pediatrician</p>
                                <div class="doctor-rating"><span class="text-warning">★★★★★</span></div>
                            </div>
                            <div class="doctor-price">$130</div>
                        </div>
                    </div>
                </div>
                
                <!-- Step 2: Date & Time -->
                <div class="step-content" id="step2-content">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="appointmentDate" class="form-label">Select Date</label>
                            <input type="date" class="form-control" id="appointmentDate" min="">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="appointmentTime" class="form-label">Select Time</label>
                            <select class="form-select" id="appointmentTime">
                                <option value="" selected disabled>Select a time</option>
                                <option value="09:00">09:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00"  class="disabled">11:00 AM</option>
                                <option value="13:00">01:00 PM</option>
                                <option value="14:00">02:00 PM</option>
                                <option value="15:00">03:00 PM</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6>Available Time Slots</h6>
                        <div class="time-slots">
                            <div class="time-slot">09:00 AM</div>
                            <div class="time-slot">10:00 AM</div>
                            <div class="time-slot unavailable">11:00 AM</div>
                            <div class="time-slot">01:00 PM</div>
                            <div class="time-slot">02:00 PM</div>
                            <div class="time-slot">03:00 PM</div>
                            <div class="time-slot">04:00 PM</div>
                        </div>
                    </div>
                </div>
                
                <!-- Step 3: Payment -->
                <div class="step-content" id="step3-content">
                    <h5 class="mb-4">Payment Method</h5>
                     <div class="payment-card">
                            <div class="d-flex align-items-center">
                                <i class="fab fa-paypal payment-icon"></i>
                                <div>
                                    <h6>AccountNO : 0619773857</h6>
                                    <p class="mb-0 text-muted">Please pay the mony with this number <br> and fill the information with the form thanks.</p>
                                </div>
                            </div>
                        </div>
                    
                    <div class="payment-methods">
                        <div class="payment-card selected">
                            <div class="d-flex align-items-center">
                                <i class="far fa-credit-card payment-icon"></i>
                                <div>
                                    <h6>Credit/Debit Card</h6>
                                    <p class="mb-0 text-muted">Pay with Visa, Mastercard,EVC Plus  or other cards</p>
                                </div>
                            </div>
                            
                            <div class="mt-4" id="creditCardForm">
                                <div class="mb-3">
                                    <label for="cardName" class="form-label">Your Name</label>
                                    <input type="text" class="form-control" id="cardName" placeholder="John Doe">
                                </div>
                                <div class="mb-3">
                                    <label for="cardNumber" class="form-label">Your Card Number</label>
                                    <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="expiryDate" class="form-label">Expiry Date</label>
                                        <input type="text" class="form-control" id="expiryDate" placeholder="MM/YY">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvv" placeholder="123">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       
                    </div>
                </div>
                
                <!-- Step 4: Confirmation -->
                <div class="step-content" id="step4-content">
                    <div class="text-center">
                        <i class="fas fa-check-circle confirmation-icon"></i>
                        <h3>Appointment Confirmed!</h3>
                        <p class="mb-4">Your appointment has been successfully scheduled</p>
                    </div>
                    
                    <div class="summary-card">
                        <h5 class="mb-3">Appointment Details</h5>
                        <div class="summary-item">
                            <span class="summary-label">Doctor:</span>
                            <span class="summary-value" id="summary-doctor">Dr. Sarah Johnson</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Date:</span>
                            <span class="summary-value" id="summary-date">June 15, 2023</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Time:</span>
                            <span class="summary-value" id="summary-time">10:00 AM</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Duration:</span>
                            <span class="summary-value">30 minutes</span>
                        </div>
                        <div class="summary-item summary-total">
                            <span class="summary-label">Total:</span>
                            <span class="summary-value" id="summary-price">$120</span>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button class="btn btn-primary" id="downloadReceipt">
                            <i class="fas fa-download me-2"></i> Download Receipt
                        </button>
                        <button class="btn btn-outline-secondary">
                            <i class="fas fa-calendar-alt me-2"></i> Add to Calendar
                        </button>
                    </div>
                </div>
                
                <!-- Navigation Buttons -->
                <div class="navigation-buttons">
                    <button class="btn btn-outline-secondary" id="prevBtn" disabled>
                        <i class="fas fa-arrow-left me-2"></i> Previous
                    </button>
                    <button class="btn btn-primary" id="nextBtn">
                        Next <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jsPDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set min date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('appointmentDate').min = today;

            // Initialize variables
            let currentStep = 1;
            let selectedDoctor = null;
            let selectedDate = null;
            let selectedTime = null;

            // Step navigation
            const nextBtn = document.getElementById('nextBtn');
            const prevBtn = document.getElementById('prevBtn');

            nextBtn.addEventListener('click', nextStep);
            prevBtn.addEventListener('click', prevStep);

            // Load doctors from backend
            fetch('../lib/api.php?action=get_doctors')
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const doctorList = document.getElementById('doctorList');
                        doctorList.innerHTML = '';
                        data.doctors.forEach(doc => {
                            const card = document.createElement('div');
                            card.className = 'doctor-card';
                            card.dataset.id = doc.id;
                            card.dataset.doctor = doc.username; // Use username from DB
                            card.dataset.specialty = doc.sepcialization; // Use sepcialization from DB
                            card.dataset.price = doc.price;
                            card.innerHTML = `
                                <img src="../uploads/${doc.img || 'default.png'}" alt="${doc.username}" class="doctor-avatar" onerror="this.onerror=null;this.src='../uploads/default.png';">
                                <div class="doctor-info">
                                    <h5 class="doctor-name">${doc.username}</h5>
                                    <p class="doctor-specialty">${doc.sepcialization}</p>
                                    <div class="doctor-rating"><span class="text-warning">★★★★★</span></div>
                                </div>
                                <div class="doctor-price">$${doc.price}</div>
                            `;
                            doctorList.appendChild(card);
                        });

                        // Attach doctor selection event
                        document.querySelectorAll('.doctor-card').forEach(card => {
                            card.addEventListener('click', function() {
                                document.querySelectorAll('.doctor-card').forEach(c => c.classList.remove('selected'));
                                this.classList.add('selected');
                                selectedDoctor = {
                                    id: this.dataset.id,
                                    name: this.dataset.doctor,
                                    specialty: this.dataset.specialty,
                                    price: this.dataset.price,
                                    image: this.querySelector('.doctor-avatar').src
                                };
                            });
                        });
                    }
                });

            // Time slot selection
            document.querySelectorAll('.time-slot:not(.unavailable)').forEach(slot => {
                slot.addEventListener('click', function() {
                    document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedTime = this.textContent;
                    document.getElementById('appointmentTime').value = this.textContent;
                });
            });
            
            // Payment method selection
            document.querySelectorAll('.payment-card').forEach(card => {
                card.addEventListener('click', function() {
                    document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });
            
            // Date selection
            document.getElementById('appointmentDate').addEventListener('change', function() {
                selectedDate = new Date(this.value).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            });
            
            // Time selection from dropdown
            document.getElementById('appointmentTime').addEventListener('change', function() {
                selectedTime = this.value;
                // Update time slot selection
                document.querySelectorAll('.time-slot').forEach(slot => {
                    slot.classList.remove('selected');
                    if (slot.textContent === this.value) {
                        slot.classList.add('selected');
                    }
                });
            });
            
            // Download receipt
            document.getElementById('downloadReceipt').addEventListener('click', function() {
                if (!selectedDoctor || !selectedDate || !selectedTime) {
                    Swal.fire('Error', 'Please complete all steps first', 'error');
                    return;
                }
                
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                
                // Add logo or header
                doc.setFontSize(20);
                doc.setTextColor(40, 40, 40);
                doc.text('Healthcare Appointment Receipt', 105, 20, { align: 'center' });
                
                // Add line
                doc.setDrawColor(115, 103, 240);
                doc.setLineWidth(0.5);
                doc.line(20, 25, 190, 25);
                
                // Add appointment details
                doc.setFontSize(12);
                doc.setTextColor(80, 80, 80);
                
                doc.text('Appointment Details:', 20, 35);
                doc.text(`Doctor: ${selectedDoctor.name}`, 20, 45);
                doc.text(`Specialty: ${selectedDoctor.specialty}`, 20, 55);
                doc.text(`Date: ${selectedDate}`, 20, 65);
                doc.text(`Time: ${selectedTime}`, 20, 75);
                
                // Add payment details
                doc.text('Payment Details:', 20, 90);
                doc.text(`Amount: $${selectedDoctor.price}`, 20, 100);
                doc.text('Payment Method: Credit Card', 20, 110);
                doc.text('Status: Paid', 20, 120);
                
                // Add footer
                doc.setFontSize(10);
                doc.setTextColor(150, 150, 150);
                doc.text('Thank you for choosing our healthcare services.', 105, 140, { align: 'center' });
                doc.text('For any questions, please contact support@healthcare.com', 105, 145, { align: 'center' });
                
                // Save the PDF
                doc.save(`Appointment_Receipt_${new Date().getTime()}.pdf`);
                
                Swal.fire('Success', 'Receipt downloaded successfully', 'success');
            });
            
            // Functions for step navigation
            function nextStep() {
                if (!validateCurrentStep()) return;

                if (currentStep === 3) {
                    const appointmentData = {
                        doctor: selectedDoctor.name, // username from DB
                        date: document.getElementById('appointmentDate').value,
                        time: document.getElementById('appointmentTime').value,
                        cardName: document.getElementById('cardName').value,
                        cardNumber: document.getElementById('cardNumber').value,
                        expiry: document.getElementById('expiryDate').value,
                        cvc: document.getElementById('cvv').value
                    };

                    Swal.fire({
                        title: 'Processing Appointment',
                        html: 'Please wait while we book your appointment...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('../lib/api.php?action=book_appointment', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(appointmentData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                title: 'Appointment Booked!',
                                html: `Your appointment with ${data.data.doctor_name} on ${data.data.appointment_date} at ${data.data.appointment_time} has been confirmed.`,
                                icon: 'success'
                            });

                            document.getElementById('summary-doctor').textContent = data.data.doctor_name;
                            document.getElementById('summary-date').textContent = data.data.appointment_date;
                            document.getElementById('summary-time').textContent = data.data.appointment_time;
                            document.getElementById('summary-price').textContent = `$${data.data.amount}`;

                            currentStep++;
                            document.getElementById(`step3-content`).classList.remove('active');
                            document.getElementById(`step3-indicator`).classList.remove('active');
                            document.getElementById(`step3-indicator`).classList.add('completed');
                            document.getElementById(`step4-content`).classList.add('active');
                            document.getElementById(`step4-indicator`).classList.add('active');
                            updateNavigationButtons();
                        } else {
                            Swal.fire('Error', data.message || 'Failed to book appointment', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'There was a problem booking your appointment. Please try again.', 'error');
                    });
                    return;
                }

                document.getElementById(`step${currentStep}-content`).classList.remove('active');
                document.getElementById(`step${currentStep}-indicator`).classList.remove('active');
                document.getElementById(`step${currentStep}-indicator`).classList.add('completed');

                currentStep++;
                document.getElementById(`step${currentStep}-content`).classList.add('active');
                document.getElementById(`step${currentStep}-indicator`).classList.add('active');
                updateNavigationButtons();

                if (currentStep === 4) {
                    updateSummary();
                }
            }

            function prevStep() {
                document.getElementById(`step${currentStep}-content`).classList.remove('active');
                document.getElementById(`step${currentStep}-indicator`).classList.remove('active');
                document.getElementById(`step${currentStep}-indicator`).classList.remove('completed');
                currentStep--;
                document.getElementById(`step${currentStep}-content`).classList.add('active');
                document.getElementById(`step${currentStep}-indicator`).classList.add('active');
                updateNavigationButtons();
            }

            function validateCurrentStep() {
                switch(currentStep) {
                    case 1:
                        if (!selectedDoctor) {
                            Swal.fire('Please Select a Doctor', 'You need to select a doctor before proceeding', 'warning');
                            return false;
                        }
                        break;
                    case 2:
                        if (!document.getElementById('appointmentDate').value) {
                            Swal.fire('Please Select a Date', 'You need to select an appointment date', 'warning');
                            return false;
                        }
                        if (!document.getElementById('appointmentTime').value) {
                            Swal.fire('Please Select a Time', 'You need to select an appointment time', 'warning');
                            return false;
                        }
                        break;
                    case 3:
                        if (!document.getElementById('cardName').value || 
                            !document.getElementById('cardNumber').value || 
                            !document.getElementById('expiryDate').value || 
                            !document.getElementById('cvv').value) {
                            Swal.fire('Payment Information Required', 'Please fill in all payment details', 'warning');
                            return false;
                        }
                        break;
                }
                return true;
            }

            function updateNavigationButtons() {
                prevBtn.disabled = currentStep === 1;
                if (currentStep === 4) {
                    nextBtn.classList.add('d-none');
                } else {
                    nextBtn.classList.remove('d-none');
                }

                if (currentStep === 3) {
                    nextBtn.innerHTML = 'Complete Booking <i class="fas fa-check ms-2"></i>';
                } else {
                    nextBtn.innerHTML = 'Next <i class="fas fa-arrow-right ms-2"></i>';
                }
            }

            function updateSummary() {
                if (selectedDoctor && selectedDate && selectedTime) {
                    document.getElementById('summary-doctor').textContent = selectedDoctor.name;
                    document.getElementById('summary-date').textContent = selectedDate;
                    document.getElementById('summary-time').textContent = selectedTime;
                    document.getElementById('summary-price').textContent = `$${selectedDoctor.price}`;
                }
            }

            // Doctor search functionality
            document.getElementById('doctorSearch').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('.doctor-card').forEach(card => {
                    const doctorName = card.dataset.doctor.toLowerCase();
                    const specialty = card.dataset.specialty.toLowerCase();
                    if (doctorName.includes(searchTerm) || specialty.includes(searchTerm)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php
