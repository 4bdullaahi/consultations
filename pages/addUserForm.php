<div class="nav-align-top">
  <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-md-0 gap-2">
    <li class="nav-item">
      <a class="nav-link <?php echo ($activeTab === 'patient') ? 'active' : ''; ?>" href="addPatient.php">
        <i class="icon-base bx bx-user icon-sm me-1_5"></i> Patient
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo ($activeTab === 'doctor') ? 'active' : ''; ?>" href="addDoctor.php">
        <i class="icon-base bx bx-bell icon-sm me-1_5"></i> Doctor
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?php echo ($activeTab === 'admin') ? 'active' : ''; ?>" href="addAdmin.php">
        <i class="icon-base bx bx-link-alt icon-sm me-1_5"></i> Admin
      </a>
    </li>
  </ul>
</div>
<div class="card mb-6">
  <!-- ...existing form code from addDoctor.php/addPatient.php... -->
  <!-- Move the form HTML here, and use PHP to customize fields as needed -->
</div>
