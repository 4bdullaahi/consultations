<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Subscription Plans - Healthcare Services</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
   <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
   <style>
      .layout-menu a,
      .menu-vertical a,
      .menu-link {
        text-decoration: none !important;
      }
    </style>
  <style>
    :root {
      --primary-color: #2a7fba;
      --secondary-color: #3bb77e;
      --dark-color: #253d4e;
      --light-color: #f8fafc;
      --border-color: #e5e5e5;
      --shadow: 0 5px 15px rgba(0, 0, 0, 0.06);
    }
    
    body {
      background-color: var(--light-color);
      font-family: 'Poppins', sans-serif;
      color: var(--dark-color);
    }
    
    /* Sidebar and content layout */
    .main-container {
      display: flex;
      height: 100vh;
      
    }
  .layout-container.sidebar {
  width: 260px;         /* Set a fixed width */
  min-width: 260px;
  max-width: 260px;
  position: sticky;
  top: 0;
  height: 200vh;
  overflow-y: auto;
  background: white;
  box-shadow: 0 0 15px rgba(0,0,0,0.1);
  z-index: 10;
  transition: width 0.3s ease;
}
.header .container {
  padding-left: 15px; /* Remove extra padding */
}
    .content-area {
  flex: 1;
  padding: 0;
  margin-left: 0; /* Match sidebar width */
  background-color: var(--light-color);
  min-height: 100vh;
}
  
    
    /* Header styling */
    .header {
      background: linear-gradient(135deg, var(--primary-color), #1a5f8b);
      color: white;
      padding: 2rem 0;
      margin-left : 14px;
      margin-bottom: 3rem;
      border-radius: 0 0 15px 15px;
    }
    
    /* Plan cards */
    .plan-card {
      background: white;
      border-radius: 12px;
      border: 1px solid var(--border-color);
      box-shadow: var(--shadow);
      padding: 2.5rem 2rem;
      transition: all 0.3s ease;
      position: relative;
      margin-bottom: 1.5rem;
      height: 100%;
    }
    
    .plan-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .plan-card.selected {
      border: 2px solid var(--primary-color);
      box-shadow: 0 0 0 4px rgba(42, 127, 186, 0.2);
    }
    
    .plan-card.popular::before {
      content: "Most Popular";
      position: absolute;
      top: -12px;
      right: 20px;
      background: var(--secondary-color);
      color: white;
      padding: 4px 15px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }
    
    .plan-icon {
      font-size: 2.5rem;
      margin-bottom: 1.5rem;
      color: var(--primary-color);
    }
    
    .plan-price {
      font-size: 2.5rem;
      font-weight: 700;
      margin: 1rem 0;
      color: var(--dark-color);
    }
    
    .plan-price small {
      font-size: 1rem;
      font-weight: 400;
      color: #7e7e7e;
    }
    
    .plan-features {
      list-style: none;
      padding: 0;
      margin: 1.5rem 0;
    }
    
    .plan-features li {
      padding: 0.5rem 0;
      position: relative;
      padding-left: 1.75rem;
    }
    
    .plan-features li:before {
      content: "\f00c";
      font-family: "Font Awesome 6 Free";
      font-weight: 900;
      position: absolute;
      left: 0;
      color: var(--secondary-color);
    }
    
    .plan-features li.disabled {
      color: #b3b3b3;
    }
    
    .plan-features li.disabled:before {
      content: "\f00d";
      color: #e74c3c;
    }
    
    .btn-plan {
      background-color: var(--primary-color);
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 500;
      transition: all 0.3s;
      width: 100%;
      margin-top: 1rem;
    }
    
    .btn-plan:hover {
      background-color: #1f6a9a;
      transform: translateY(-2px);
    }
    
    .btn-plan.selected {
      background-color: var(--secondary-color);
    }
    
    /* Payment section */
    .payment-card {
      background: white;
      border-radius: 12px;
      box-shadow: var(--shadow);
      padding: 2rem;
      margin-top: 2rem;
    }
    
    /* Receipt section */
    .receipt-card {
      background: white;
      border-radius: 12px;
      box-shadow: var(--shadow);
      padding: 2.5rem;
      margin: 3rem auto;
      max-width: 700px;
    }
    
    .receipt-header {
      text-align: center;
      margin-bottom: 2rem;
    }
    
    .receipt-header i {
      font-size: 3rem;
      color: var(--secondary-color);
      margin-bottom: 1rem;
    }
    
    .receipt-details {
      margin-bottom: 2rem;
    }
    
    .receipt-details p {
      margin-bottom: 0.75rem;
    }
    
    .receipt-divider {
      border-top: 2px dashed var(--border-color);
      margin: 2rem 0;
    }
    
    .hidden {
      display: none;
    }
    
    /* Feature comparison table */
    .feature-comparison {
      margin: 3rem 0;
    }
    
    .feature-comparison th {
      font-weight: 600;
      background-color: #f8f9fa;
    }
    
    /* Tabs styling */
    .nav-tabs .nav-link.active {
      color: var(--primary-color);
      font-weight: 600;
      border-bottom: 3px solid var(--primary-color);
    }
    
    .nav-tabs .nav-link {
      color: #6c757d;
      font-weight: 500;
    }
    
  
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
      .main-container {
        flex-direction: column;
      }
      
      
      
      .content-area {
        padding: 20px 15px;
      }
    }
    
    @media (max-width: 768px) {
      .plan-card {
        padding: 1.5rem;
      }
      
      .plan-price {
        font-size: 2rem;
      }
      
      .header {
        padding: 1.5rem 0;
      }
    }
  </style>
</head>

<body>
  <div class="main-container">
    <div class="layout-container sidebar">
    <!-- Sidebar -->
   
      <?php include '../lib/pSidebar.php' ?>
   
  </div>
    <!-- Main Content Area -->
    <div class="content-area">
      <div class="header">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h1 class="fw-bold mb-3">Choose Your Healthcare Plan</h1>
              <p class="lead mb-0">Select the plan that best fits your healthcare needs and budget.</p>
            </div>
            <div class="col-md-4 text-md-end">
              <span class="badge bg-light text-dark fs-6"><i class="fas fa-shield-alt me-2"></i>Secure Payment</span>
            </div>
          </div>
        </div>
      </div>

      <div class="container">
        <div class="text-center mb-5">
          <ul class="nav nav-tabs justify-content-center border-0" id="planTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="monthly-tab" data-bs-toggle="tab" data-bs-target="#monthly" type="button" role="tab">Monthly Billing</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="annual-tab" data-bs-toggle="tab" data-bs-target="#annual" type="button" role="tab">Annual Billing (Save 20%)</button>
            </li>
          </ul>
        </div>

        <!-- Plans Section -->
        <div class="tab-content" id="planTabsContent">
          <div class="tab-pane fade show active" id="monthly" role="tabpanel">
            <div class="row g-4">
              <div class="col-lg-4">
                <div class="plan-card text-center">
                  <div class="plan-icon"><i class="fas fa-star"></i></div>
                  <h4 class="fw-bold">Basic Plan</h4>
                  <p class="text-muted">Essential healthcare services</p>
                  <div class="plan-price">$10<small>/month</small></div>
                  <ul class="plan-features">
                    <li>10 Appointments/Month</li>
                    <li>Email Support (48hrs)</li>
                    <li>AI Symptom Checker</li>
                    <li class="disabled">No Doctor Chat</li>
                    <li class="disabled">No Reschedule Option</li>
                  </ul>
                  <button class="btn btn-plan" data-plan="Basic" data-price="10" data-duration="Monthly">Select Plan</button>
                </div>
              </div>
              
              <div class="col-lg-4">
                <div class="plan-card text-center popular">
                  <div class="plan-icon"><i class="fas fa-rocket"></i></div>
                  <h4 class="fw-bold">Standard Plan</h4>
                  <p class="text-muted">Comprehensive healthcare</p>
                  <div class="plan-price">$20<small>/month</small></div>
                  <ul class="plan-features">
                    <li>Unlimited Appointments</li>
                    <li>Priority Email + SMS Support</li>
                    <li>Assigned General Physician</li>
                    <li>Rescheduling Option (2x/Month)</li>
                    <li>Prescription Access</li>
                  </ul>
                  <button class="btn btn-plan selected" data-plan="Standard" data-price="20" data-duration="Monthly">Select Plan</button>
                </div>
              </div>
              
              <div class="col-lg-4">
                <div class="plan-card text-center">
                  <div class="plan-icon"><i class="fas fa-crown"></i></div>
                  <h4 class="fw-bold">Premium Plan</h4>
                  <p class="text-muted">Premium healthcare experience</p>
                  <div class="plan-price">$30<small>/month</small></div>
                  <ul class="plan-features">
                    <li>All Standard Features</li>
                    <li>24/7 Doctor Chat + Video Call</li>
                    <li>Specialist Access</li>
                    <li>Family Plan (2 Extra Members)</li>
                    <li>Annual Check-Up Coupon</li>
                  </ul>
                  <button class="btn btn-plan" data-plan="Premium" data-price="30" data-duration="Monthly">Select Plan</button>
                </div>
              </div>
            </div>
          </div>
          
          <div class="tab-pane fade" id="annual" role="tabpanel">
            <div class="row g-4">
              <div class="col-lg-4">
                <div class="plan-card text-center">
                  <div class="plan-icon"><i class="fas fa-star"></i></div>
                  <h4 class="fw-bold">Basic Plan</h4>
                  <p class="text-muted">Essential healthcare services</p>
                  <div class="plan-price">$96<small>/year</small></div>
                  <p class="text-success fw-bold">Save $24 annually</p>
                  <ul class="plan-features">
                    <li>10 Appointments/Month</li>
                    <li>Email Support (48hrs)</li>
                    <li>AI Symptom Checker</li>
                    <li class="disabled">No Doctor Chat</li>
                    <li class="disabled">No Reschedule Option</li>
                  </ul>
                  <button class="btn btn-plan" data-plan="Basic" data-price="96" data-duration="Annual">Select Plan</button>
                </div>
              </div>
              
              <div class="col-lg-4">
                <div class="plan-card text-center popular">
                  <div class="plan-icon"><i class="fas fa-rocket"></i></div>
                  <h4 class="fw-bold">Standard Plan</h4>
                  <p class="text-muted">Comprehensive healthcare</p>
                  <div class="plan-price">$192<small>/year</small></div>
                  <p class="text-success fw-bold">Save $48 annually</p>
                  <ul class="plan-features">
                    <li>Unlimited Appointments</li>
                    <li>Priority Email + SMS Support</li>
                    <li>Assigned General Physician</li>
                    <li>Rescheduling Option (2x/Month)</li>
                    <li>Prescription Access</li>
                  </ul>
                  <button class="btn btn-plan selected" data-plan="Standard" data-price="192" data-duration="Annual">Select Plan</button>
                </div>
              </div>
              
              <div class="col-lg-4">
                <div class="plan-card text-center">
                  <div class="plan-icon"><i class="fas fa-crown"></i></div>
                  <h4 class="fw-bold">Premium Plan</h4>
                  <p class="text-muted">Premium healthcare experience</p>
                  <div class="plan-price">$288<small>/year</small></div>
                  <p class="text-success fw-bold">Save $72 annually</p>
                  <ul class="plan-features">
                    <li>All Standard Features</li>
                    <li>24/7 Doctor Chat + Video Call</li>
                    <li>Specialist Access</li>
                    <li>Family Plan (2 Extra Members)</li>
                    <li>Annual Check-Up Coupon</li>
                  </ul>
                  <button class="btn btn-plan" data-plan="Premium" data-price="288" data-duration="Annual">Select Plan</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Feature Comparison -->
        <div class="feature-comparison">
          <h3 class="text-center mb-4 fw-bold">Plan Comparison</h3>
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Feature</th>
                  <th class="text-center">Basic</th>
                  <th class="text-center">Standard</th>
                  <th class="text-center">Premium</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Monthly Appointments</td>
                  <td class="text-center">10</td>
                  <td class="text-center">Unlimited</td>
                  <td class="text-center">Unlimited</td>
                </tr>
                <tr>
                  <td>Support Response Time</td>
                  <td class="text-center">48 hours</td>
                  <td class="text-center">24 hours</td>
                  <td class="text-center">Instant (24/7)</td>
                </tr>
                <tr>
                  <td>Doctor Communication</td>
                  <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                  <td class="text-center">Assigned Physician</td>
                  <td class="text-center">Specialists + Video</td>
                </tr>
                <tr>
                  <td>Family Members</td>
                  <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                  <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                  <td class="text-center">2 Extra Members</td>
                </tr>
                <tr>
                  <td>Prescription Access</td>
                  <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                  <td class="text-center"><i class="fas fa-check text-success"></i></td>
                  <td class="text-center"><i class="fas fa-check text-success"></i></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Payment Section -->
        <div id="paymentSection" class="hidden">
          <div class="payment-card">
            <div class="row">
              <div class="col-lg-6">
                <h3 class="fw-bold mb-4">Payment Details</h3>
                 <div class="payment-card">
                            <div class="d-flex align-items-center">
                                <i class="fab fa-paypal payment-icon"></i>
                                <div>
                                    <h6>AccountNO : 0619773857</h6>
                                    <p class="mb-0 text-muted">Please pay the mony with this number <br> and fill the information with the form thanks.</p>
                                </div>
                            </div>
                        </div>
                <form id="paymentForm">
                  <div class="mb-3">
                    <label for="cardName" class="form-label">Cardholder Name</label>
                    <input type="text" class="form-control" id="cardName" placeholder="John Doe" required>
                  </div>
                  <div class="mb-3">
                    <label for="cardNumber" class="form-label">Card Number</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456" required>
                      <span class="input-group-text"><i class="fab fa-cc-visa"></i> <i class="fab fa-cc-mastercard ms-2"></i> <i class="fab fa-cc-amex ms-2"></i></span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="expiry" class="form-label">Expiry Date</label>
                      <input type="text" class="form-control" id="expiry" placeholder="MM/YY" required>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="cvc" class="form-label">Security Code</label>
                      <input type="text" class="form-control" id="cvc" placeholder="CVC" required>
                    </div>
                  </div>
                  <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="saveCard">
                    <label class="form-check-label" for="saveCard">Save card for future payments</label>
                  </div>
                  <button type="submit" class="btn btn-primary btn-lg w-100" id="payBtn">Complete Payment</button>
                </form>
              </div>
              <div class="col-lg-5 offset-lg-1">
                <div class="bg-light p-4 rounded">
                  <h4 class="fw-bold mb-4">Order Summary</h4>
                  <div class="d-flex justify-content-between mb-3">
                    <span>Plan:</span>
                    <span id="summaryPlan" class="fw-bold">Standard Plan</span>
                  </div>
                  <div class="d-flex justify-content-between mb-3">
                    <span>Billing Cycle:</span>
                    <span id="summaryDuration" class="fw-bold">Monthly</span>
                  </div>
                  <div class="d-flex justify-content-between mb-3">
                    <span>Subtotal:</span>
                    <span id="summaryPrice" class="fw-bold">$20.00</span>
                  </div>
                  <div class="d-flex justify-content-between mb-3">
                    <span>Tax:</span>
                    <span class="fw-bold">$0.00</span>
                  </div>
                  <hr>
                  <div class="d-flex justify-content-between mb-3">
                    <span class="fw-bold">Total:</span>
                    <span id="summaryTotal" class="fw-bold">$20.00</span>
                  </div>
                  <div class="alert alert-success mt-4">
                    <i class="fas fa-lock me-2"></i> Your payment is secure and encrypted
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Receipt Section -->
        <div id="receipt" class="hidden">
          <div class="receipt-card">
            <div class="receipt-header">
              <i class="fas fa-check-circle"></i>
              <h3 class="fw-bold">Payment Successful!</h3>
              <p class="text-muted">Your subscription has been activated</p>
            </div>
            
            <div class="receipt-details">
              <h5 class="fw-bold mb-3">Order Details</h5>
              <div class="row">
                <div class="col-md-6">
                  <p><strong>Transaction ID:</strong> <span id="receiptId">HG-789456123</span></p>
                  <p><strong>Date:</strong> <span id="receiptDate">June 16, 2023</span></p>
                  <p><strong>Time:</strong> <span id="receiptTime">10:45 AM</span></p>
                </div>
                <div class="col-md-6">
                  <p><strong>Payment Method:</strong> <span id="receiptMethod">VISA •••• 3456</span></p>
                  <p><strong>Billing Email:</strong> <span id="receiptEmail">user@example.com</span></p>
                </div>
              </div>
            </div>
            
            <div class="receipt-divider"></div>
            
            <div class="receipt-details">
              <h5 class="fw-bold mb-3">Subscription Details</h5>
              <div class="row">
                <div class="col-md-6">
                  <p><strong>Plan:</strong> <span id="receiptPlan">Standard Plan</span></p>
                  <p><strong>Billing Cycle:</strong> <span id="receiptCycle">Monthly</span></p>
                </div>
                <div class="col-md-6">
                  <p><strong>Amount Paid:</strong> <span id="receiptAmount">$20.00</span></p>
                  <p><strong>Next Billing Date:</strong> <span id="receiptNext">July 16, 2023</span></p>
                </div>
              </div>
            </div>
            
            <div class="receipt-divider"></div>
            
            <div class="text-center">
              <p class="text-muted">A confirmation has been sent to your email address.</p>
              <div class="d-flex justify-content-center gap-3 mt-4">
                <button class="btn btn-outline-primary" id="downloadReceipt"><i class="fas fa-download me-2"></i>Download Receipt</button>
                <button class="btn btn-primary" id="goToDashboard"><i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      let selectedPlan = '';
      let selectedPrice = '';
      let selectedDuration = '';
      
      // Plan selection
      document.querySelectorAll('.btn-plan').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Remove selected class from all buttons
          document.querySelectorAll('.btn-plan').forEach(b => {
            b.classList.remove('selected');
            b.textContent = 'Select Plan';
          });
          
          // Add selected class to clicked button
          this.classList.add('selected');
          this.textContent = 'Selected ✓';
          
          // Set selected plan details
          selectedPlan = this.dataset.plan;
          selectedPrice = this.dataset.price;
          selectedDuration = this.dataset.duration;
          
          // Update order summary
          document.getElementById('summaryPlan').textContent = selectedPlan + ' Plan';
          document.getElementById('summaryDuration').textContent = selectedDuration;
          document.getElementById('summaryPrice').textContent = '$' + selectedPrice + '.00';
          document.getElementById('summaryTotal').textContent = '$' + selectedPrice + '.00';
          
          // Show payment section
          document.getElementById('paymentSection').classList.remove('hidden');
          
          // Scroll to payment section
          document.getElementById('paymentSection').scrollIntoView({ behavior: 'smooth' });
          
          // Show success message
          Swal.fire({
            icon: 'success',
            title: 'Plan Selected',
            text: `You've selected the ${selectedPlan} Plan (${selectedDuration})`,
            confirmButtonColor: '#2a7fba'
          });
        });
      });
      
      // Payment form submission
      document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const cardName = document.getElementById('cardName').value.trim();
        const cardNumber = document.getElementById('cardNumber').value.trim();
        const expiry = document.getElementById('expiry').value.trim();
        const cvc = document.getElementById('cvc').value.trim();
        
        if (!cardName || !cardNumber || !expiry || !cvc) {
          Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please complete all payment details',
            confirmButtonColor: '#2a7fba'
          });
          return;
        }
        
        // Validate card number (simple validation)
        if (cardNumber.replace(/\s/g, '').length < 16) {
          Swal.fire({
            icon: 'error',
            title: 'Invalid Card',
            text: 'Please enter a valid 16-digit card number',
            confirmButtonColor: '#2a7fba'
          });
          return;
        }
        
        // Validate expiry date
        if (!/^\d{2}\/\d{2}$/.test(expiry)) {
          Swal.fire({
            icon: 'error',
            title: 'Invalid Expiry',
            text: 'Please enter expiry date in MM/YY format',
            confirmButtonColor: '#2a7fba'
          });
          return;
        }
        
        // Validate CVC
        if (cvc.length < 3) {
          Swal.fire({
            icon: 'error',
            title: 'Invalid CVC',
            text: 'Please enter a valid 3-4 digit security code',
            confirmButtonColor: '#2a7fba'
          });
          return;
        }
        
        // Show loading state
        const payBtn = document.getElementById('payBtn');
        payBtn.disabled = true;
        payBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
        
        // Simulate API call
        setTimeout(() => {
          // Generate random transaction ID
          const transactionId = 'HG-' + Math.floor(100000000 + Math.random() * 900000000);
          
          // Get current date and time
          const now = new Date();
          const options = { year: 'numeric', month: 'long', day: 'numeric' };
          const dateStr = now.toLocaleDateString('en-US', options);
          const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
          
          // Calculate next billing date
          let nextBillingDate = new Date(now);
          if (selectedDuration === 'Monthly') {
            nextBillingDate.setMonth(nextBillingDate.getMonth() + 1);
          } else {
            nextBillingDate.setFullYear(nextBillingDate.getFullYear() + 1);
          }
          const nextBillingStr = nextBillingDate.toLocaleDateString('en-US', options);
          
          // AJAX call to save subscription
          fetch('../saves/save_subscription.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
              plan_name: selectedPlan,
              plan_price: selectedPrice,
              billing_cycle: selectedDuration,
              cardholder_name: cardName,
              card_number: cardNumber,
              expiry_date: expiry,
              transaction_id: transactionId,
              next_billing_date: nextBillingDate.toISOString().slice(0, 19).replace('T', ' ')
            })
          })
          .then(res => res.json())
          .then(response => {
            if (response.success) {
              // Update receipt details as before
              document.getElementById('receiptId').textContent = transactionId;
              document.getElementById('receiptDate').textContent = dateStr;
              document.getElementById('receiptTime').textContent = timeStr;
              document.getElementById('receiptMethod').textContent = 'VISA •••• ' + cardNumber.slice(-4);
              document.getElementById('receiptPlan').textContent = selectedPlan + ' Plan';
              document.getElementById('receiptCycle').textContent = selectedDuration;
              document.getElementById('receiptAmount').textContent = '$' + selectedPrice + '.00';
              document.getElementById('receiptNext').textContent = nextBillingStr;

              document.getElementById('paymentSection').classList.add('hidden');
              document.getElementById('receipt').classList.remove('hidden');
              document.getElementById('receipt').scrollIntoView({ behavior: 'smooth' });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.message,
                confirmButtonColor: '#2a7fba'
              });
            }
            payBtn.disabled = false;
            payBtn.textContent = 'Complete Payment';
          });
        }, 2000);
      });
      
      // Download receipt
      document.getElementById('downloadReceipt').addEventListener('click', function() {
        // In a real app, this would generate a PDF receipt
        Swal.fire({
          icon: 'success',
          title: 'Receipt Downloaded',
          text: 'Your receipt has been downloaded successfully',
          confirmButtonColor: '#2a7fba'
        });
      });
      
      // Go to dashboard
      document.getElementById('goToDashboard').addEventListener('click', function() {
        // In a real app, this would redirect to the user dashboard
        Swal.fire({
          icon: 'success',
          title: 'Welcome to Your Dashboard',
          text: 'You can now access all your healthcare services',
          confirmButtonColor: '#2a7fba'
        });
      });
    });
  </script>
</body>
</html>