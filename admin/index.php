<?php 
    @include 'config.php';

    session_start();

    if(!isset($_SESSION['admin_name'])){
        header('location:login.php');
    }
?>

<?php 
    include('inc/header.php');
    include('inc/topbar.php');
    include('inc/sidebar.php');

    // Count pending learning space bookings
    $queryPendingBookings = "SELECT COUNT(*) AS pending_bookings FROM bookings WHERE status = 'Pending'";
    $resultPendingBookings = $conn->query($queryPendingBookings);
    $rowPendingBookings = $resultPendingBookings->fetch_assoc();
    $pendingBookingsCount = $rowPendingBookings['pending_bookings'];

    // Count pending tool bookings
    $queryPendingToolBookings = "SELECT COUNT(*) AS pending_tool_bookings FROM tools_bookings WHERE status = 'Pending'";
    $resultPendingToolBookings = $conn->query($queryPendingToolBookings);
    $rowPendingToolBookings = $resultPendingToolBookings->fetch_assoc();
    $pendingToolBookingsCount = $rowPendingToolBookings['pending_tool_bookings'];

    // Count total registered users
    $queryTotalUsers = "SELECT COUNT(*) AS total_users FROM user_db";
    $resultTotalUsers = mysqli_query($conn, $queryTotalUsers);
    $rowTotalUsers = mysqli_fetch_assoc($resultTotalUsers);
    $totalUsers = $rowTotalUsers['total_users'];

    // Count total unread inquiries
    $queryTotalUnread = "SELECT COUNT(*) AS total_unread FROM user_queries WHERE seen = 0";
    $resultTotalUnread = mysqli_query($conn, $queryTotalUnread);
    $rowTotalUnread = mysqli_fetch_assoc($resultTotalUnread);
    $totalUnread = $rowTotalUnread['total_unread'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Dashboard</title>
 
</head>
<body>
<?
    // Check if building is specified in the GET parameters
    if (isset($_GET['building'])) {
        $selectedBuilding = urldecode($_GET['building']);

        // Fetch learning space options based on the selected building
        $queryLearningSpaces = "SELECT DISTINCT space_name FROM ums_learning_spaces WHERE building = '$selectedBuilding'";
        $resultLearningSpaces = mysqli_query($conn, $queryLearningSpaces);

        $options = [];

        while ($rowLearningSpaces = mysqli_fetch_assoc($resultLearningSpaces)) {
            $options[] = $rowLearningSpaces['space_name'];
        }

        // Fetch learning spaces based on selected filters
        $querySpaces = "SELECT * FROM ums_learning_spaces WHERE building = '$selectedBuilding'";

        $resultSpaces = mysqli_query($conn, $querySpaces);

        $learningSpaces = '';

        // Loop through the result to create HTML options
        while ($rowSpaces = mysqli_fetch_assoc($resultSpaces)) {
            $learningSpaces .= "<option value='{$rowSpaces['space_name']}'>{$rowSpaces['space_name']}</option>";
        }

        // Return data as JSON including options
        echo json_encode(['options' => $options, 'learningSpaces' => $learningSpaces]);
        exit(); // Stop further execution after sending JSON response
    }
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box for learning space bookings-->
              <div class="small-box bg-info">
                  <div class="inner">
                      <h3><?php echo $pendingBookingsCount; ?></h3>
                      <p>Space Bookings Approval Pending</p>
                  </div>
                  <div class="icon">
                      <i class="ion bi-bookmark-check-fill nav-icon"></i>
                  </div>
                  <a href="user_bookings.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box for tool bookings -->
              <div class="small-box bg-primary">
                  <div class="inner">
                      <h3><?php echo $pendingToolBookingsCount; ?></h3>
                      <p>Tool Bookings Approval Pending</p>
                  </div>
                  <div class="icon">
                      <i class="ion bi-tools"></i>
                  </div>
                  <a href="user_bookings.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-secondary">
                  <div class="inner">
                      <h3><?php echo $totalUsers; ?></h3>
                      <p>Users Registered</p>
                  </div>
                  <div class="icon">
                      <i class="ion bi-people-fill nav-icon"></i>
                  </div>
                  <a href="registered.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>          
            <!-- ./col -->
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-danger">
                  <div class="inner">
                      <h3><?php echo $totalUnread; ?></h3>
                      <p>Enquiries Received</p>
                  </div>
                  <div class="icon">
                      <i class="ion bi-chat-left-text-fill nav-icon"></i>
                  </div>
                  <a href="enquiries.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
        </div>
    </section>

    
    <section class="container-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Check for total bookings</h4>
                        </div>
                        <div class="border bg-light p-3 rounded mb-3">
                            <div class="row">
                                <!-- Building Section -->
                                <div class="col-lg-6">
                                    <h5 class="mb-3" style="font-size: 20px;">Building:</h5>
                                    <select id="building-select" class="form-select shadow-none" aria-label="Default select example">
                                        <?php
                                        $buildingOptions = ["Bangunan Podium", "Bangunan Menara", "Dewan Kuliah Pusat", "Makmal Komputeran"];
                                        foreach ($buildingOptions as $option) {
                                            echo "<option value='$option'>$option</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                        
                                <!-- Time Range Section -->
                                <div class="col-lg-6">
                                    <h5 class="mb-3" style="font-size: 20px;">Time Range:</h5>
                                    <select id="time-range-select" class="form-select shadow-none" aria-label="Default select example">
                                        <option value="1">1 month ago</option>
                                        <option value="3">3 months ago</option>
                                        <option value="12">1 year ago</option>
                                        <option value="24">2 years ago</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                            <div class="card-body">
                                <table id="bookings-table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Learning Space</th>
                                            <th>Total Bookings</th>    
                                        </tr>
                                    </thead>
                                    <tbody id="bookings-body">
                                        <!-- Bookings data will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buildingSelect = document.getElementById('building-select');
        const timeRangeSelect = document.getElementById('time-range-select');
        const bookingsTableBody = document.getElementById('bookings-body');

        function updateTotalBookings() {
            const selectedBuilding = buildingSelect.value;
            const selectedTimeRange = timeRangeSelect.value;

            // Calculate the start date based on the selected time range
            const currentDate = new Date();
            const startDate = new Date(currentDate);
            startDate.setMonth(currentDate.getMonth() - parseInt(selectedTimeRange));

            const formattedStartDate = startDate.toISOString().split('T')[0]; // Format as 'YYYY-MM-DD'

            const xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);

                    // Clear previous data
                    bookingsTableBody.innerHTML = '';

                    // Populate bookings data
                    data.data.forEach((row, index) => {
                        const newRow = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${row[0]}</td>
                                <td>${row[1]}</td>
                            </tr>
                        `;
                        bookingsTableBody.innerHTML += newRow;
                    });
                }
            };

            xhr.open('GET', 'bookings_data.php?building=' + selectedBuilding + '&date=' + formattedStartDate, true);
            xhr.send();
        }

        buildingSelect.addEventListener('change', updateTotalBookings);
        timeRangeSelect.addEventListener('change', updateTotalBookings);

        // Initial update
        updateTotalBookings();
    });
</script>
</body>
</html>
<?php include('inc/scripts.php'); ?>