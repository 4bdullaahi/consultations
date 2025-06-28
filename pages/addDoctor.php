<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../index.php");
  exit();
}
$activeTab = 'doctor';
?>
<!doctype html>

<html
  lang="en"
  class="layout-menu-fixed layout-compact"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>User Registeration-Admin</title>

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

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->

    <link rel="stylesheet" href="../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- endbuild -->

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="../assets/js/config.js"></script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <?php require_once("../lib/sidebar.php"); ?>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="row">
                <div class="col-md-12">
                 <div class="row">
                <div class="col-md-12">
                  <div class="nav-align-top">
                    <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
                      <li class="nav-item">
                        <a class="nav-link " href="../pages/addPatient.php"
                          ><i class="icon-base bx bx-user icon-sm me-1_5"></i> Patient</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link " href="../pages/addDoctor.php"
                          ><i class="icon-base bx bx-user icon-sm me-1_5"></i> Doctors</a
                        >
                      </li>
                      <li class="nav-item">
                        <a class="nav-link " href="../pages/addAdmin.php"
                          ><i class="icon-base bx bx-user icon-sm me-1_5"></i> Admin</a
                        >
                      </li>
                    </ul>
                  </div>
                  <div class="card mb-6">
                    <!-- Account -->
                    
                    <div class="card-body pt-4">
                      <form id="formAccountSettings" method="POST" action="../saves/SaveDoctor.php" enctype="multipart/form-data">
                        <div class="card-body">
                      <div class="d-flex align-items-start align-items-sm-center gap-6 pb-4 border-bottom">
                        <img
                          src="../assets/img/avatars/1.png"
                          alt="user-avatar"
                          class="d-block w-px-100 h-px-100 rounded"
                          id="uploadedAvatar" />
                        <div class="button-wrapper">
                          <label for="upload" class="btn btn-primary me-3 mb-4" tabindex="0">
                            <span class="d-none d-sm-block">Upload Doctor photo</span>
                            <i class="icon-base bx bx-upload d-block d-sm-none"></i>
                            <input
                              type="file"
                              id="upload"
                              name="photo"
                              class="account-file-input"
                              hidden
                              accept="image/png, image/jpeg" />
                          </label>
                          <button type="button" class="btn btn-outline-secondary account-image-reset mb-4">
                            <i class="icon-base bx bx-reset d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Reset</span>
                          </button>

                          <div>Allowed JPG, GIF or PNG. Max size of 800K</div>
                        </div>
                      </div>
                    </div>
                        <div class="row g-6">
                          <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <input
                              class="form-control"
                              type="text"
                              required
                              id="username"
                              name="username"
                              placeholder="Dr muuse"
                              autofocus />
                          </div>
                          <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <input class="form-control" type="password" name="password" id="password" placeholder="*******" />
                          </div>
                          <div class="col-md-6">
                            <label for="specialization" class="form-label">Specialization</label>
                            <input
                              class="form-control"
                              type="text"
                              required
                              id="specialization"
                              name="specialization"
                              placeholder="cardiology" />
                          </div>
                          <div class="col-md-6">
                            <label for="organization" class="form-label">Price</label>
                            <input
                              type="double"
                              class="form-control"
                              id="price"
                              name="price"
                              placeholder="$1200">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label" for="phoneNumber">Phone Number</label>
                            <div class="input-group input-group-merge">
                              <span class="input-group-text">so (+252)</span>
                              <input
                                type="text"
                                id="phone"
                                name="phone"
                                class="form-control"
                                placeholder="619773857" />
                            </div>
                          </div>
                          <div class="col-md-6">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="Address" />
                          </div>
                          <div class="col-md-6">
                            <label for="Email" class="form-label">Email</label>
                            <input class="form-control" type="text" id="email" name="email" placeholder="muuse@gmail.com" />
                          </div>

                        </div>
                        <div class="mt-6">
                          <button type="submit" class="btn btn-primary me-3">Save changes</button>
                          <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                        </div>
                      </form>
                    </div>
                    <!-- /Account -->
                  </div>
              
                </div>
              </div>
                </div>
              </div>
            </div>
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl">
                <div
                  class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                  <div class="mb-2 mb-md-0">
                    &#169;
                    <script>
                      document.write(new Date().getFullYear());
                    </script>
                    , made with ❤️ by
                    <a href="https://themeselection.com" target="_blank" class="footer-link">ThemeSelection</a>
                  </div>
                  <div class="d-none d-lg-inline-block">
                    <a
                      href="https://themeselection.com/item/category/admin-templates/"
                      target="_blank"
                      class="footer-link me-4"
                      >Admin Templates</a
                    >

                    <a href="https://themeselection.com/license/" class="footer-link me-4" target="_blank">License</a>
                    <a
                      href="https://themeselection.com/item/category/bootstrap-admin-templates/"
                      target="_blank"
                      class="footer-link me-4"
                      >Bootstrap Dashboard</a
                    >

                    <a
                      href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/documentation/"
                      target="_blank"
                      class="footer-link me-4"
                      >Documentation</a
                    >

                    <a
                      href="https://github.com/themeselection/sneat-bootstrap-html-admin-template-free/issues"
                      target="_blank"
                      class="footer-link"
                      >Support</a
                    >
                  </div>
                </div>
              </div>
            </footer>
            <!-- / Footer -->

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

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->

    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../assets/js/pages-account-settings-account.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('formAccountSettings');
  form.addEventListener('submit', function(e) {
    e.preventDefault();

    // Validation
    const username = form.username.value.trim();
    const specialization = form.specialization.value.trim();
    const phone = form.phone.value.trim();
    const price = form.price.value.trim();

    // Name validation: only letters and spaces
    const nameRegex = /^[A-Za-z\s]+$/;
    if (!nameRegex.test(username)) {
      Swal.fire({icon: 'error', title: 'Invalid Name', text: 'Doctor name must contain only letters and spaces.'});
      return;
    }
    if (!nameRegex.test(specialization)) {
      Swal.fire({icon: 'error', title: 'Invalid Specialization', text: 'Specialization must contain only letters and spaces.'});
      return;
    }

    // Phone validation: exactly 9 digits
    const phoneRegex = /^\d{9}$/;
    if (!phoneRegex.test(phone)) {
      Swal.fire({icon: 'error', title: 'Invalid Phone', text: 'Phone number must be exactly 9 digits.'});
      return;
    }

    // Price validation: must be a number (integer or decimal)
    if (price && isNaN(price)) {
      Swal.fire({icon: 'error', title: 'Invalid Price', text: 'Price must be a number.'});
      return;
    }

    // If all validations pass, submit via AJAX
    const formData = new FormData(form);
    fetch(form.action, {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(text => {
      console.log('Server response:', text); // Add this line
      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Server returned invalid response: ' + text
        });
        return;
      }
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Doctor saved!',
          text: data.message || 'Doctor registered successfully.'
        });
        form.reset();
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.message || 'Failed to save doctor.'
        });
      }
    })
    .catch(error => {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'An error occurred while saving doctor.'
      });
    });
  });

  // Optional: Prevent typing non-numeric in phone and price fields
  form.phone.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '').slice(0,9);
  });
  form.price.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9.]/g, '');
  });
  form.username.addEventListener('input', function() {
    this.value = this.value.replace(/[^A-Za-z\s]/g, '');
  });
  form.specialization.addEventListener('input', function() {
    this.value = this.value.replace(/[^A-Za-z\s]/g, '');
  });
});
</script>

    <!-- Place this tag before closing body tag for github widget button. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
<?php
echo json_encode(['success' => false, 'message' => 'Error message here']);
exit;
?>
