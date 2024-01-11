<?php 
@include 'config.php';
  
if(!isset($_SESSION['admin_name'])){
	header('location:login.php');
}
?>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!--Logo -->
    <a href="index.php" class="brand-link">
      <img src="assets/dist/img/Logo.png" alt="Logo" class="brand-image img-circle elevation-4" style="opacity: .8">
      <span class="navbar-brand me-5 fw-bold fs-3 h-font">LecRoom</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="info">
          <a href="index.php" class="d-block">Welcome, <?php echo $_SESSION['admin_name'] ?></a>
        </div>
      </div>


      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="index.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <!--Space-->
          <li class="nav-item">
            <a href="learning_space_management.php" class="nav-link">
              <i class="far bi-building-fill nav-icon"></i>
              <p>Learning Space</p>
            </a>
          </li>
          <!--Equipment-->
          <li class="nav-item">
            <a href="tool_management.php" class="nav-link">
              <i class="far bi-tools nav-icon"></i>
              <p>
                Tools And Accessories
              </p>
            </a>
          </li>

          <li class="nav-header">User Management</li>
          <li class="nav-item">
            <a href="registered.php" class="nav-link">
              <i class="far bi-people-fill nav-icon"></i>
              <p>
                Registered Users
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="user_bookings.php" class="nav-link">
              <i class="far bi-bookmark-check-fill nav-icon"></i>
              <p>
                Users Bookings
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="enquiries.php" class="nav-link">
              <i class="far bi-chat-left-text-fill nav-icon"></i>
              <p>
                User Enquiries
              </p>
            </a>
          </li>


  
          
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>