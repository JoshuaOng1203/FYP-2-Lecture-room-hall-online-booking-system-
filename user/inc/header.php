<?php 
	@include 'config.php';
	if(!isset($_SESSION['user_name']) || !isset($_SESSION['user_id'])){
		header('location:login.php');
	}
?>

<nav class="navbar navbar-expand-lg bg-info px-lg-3 py-lg-2 shadow-sm sticky-top">
	<div class="container-fluid">
		<a class="navbar-brand me-5 fw-bold fs-3 h-font" href="index.php">LecRoom</a>
		<button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
				<li class="nav-item">
				<a class="nav-link active" aria-current="page" href="index.php">Home</a>
				</li>
				<li class="nav-item">
				<a class="nav-link me-2" href="spaces.php">Learning Space</a>
				</li>
				<li class="nav-item">
				<a class="nav-link me-2" href="tools.php">Tool</a>
				</li>
				<li class="nav-item">
				<a class="nav-link me-2" href="mybooking.php">My Bookings</a>
				</li>
				<li class="nav-item">
				<a class="nav-link me-2" href="contact.php">Contact Us</a>
				</li>
			</ul>			
		</div>
			<ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">Welcome, <?php echo $_SESSION['user_name'] ?></a>
					<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
						<li><a class="dropdown-item" href="profile.php">My Profile</a></li>
						<li><a class="dropdown-item" href="logout.php">Logout</a></li>
					</ul>
				</li>
			</ul>
  	</div>
</nav>

