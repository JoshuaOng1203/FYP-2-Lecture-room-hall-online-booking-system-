<?php 
	@include 'config.php';
	session_start();
?>

<?php 
    include('inc/header.php');
    include('inc/topbar.php');
    include('inc/sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Bookings Management Page</title>
  
</head>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="container-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Learning Space Bookings</h4>                            
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone Number</th>
                                        <th>Email</th>
                                        <th>Space Name</th>
                                        <th>Date</th>
                                        <th>Timeslot</th>
                                        <th>Coordinator Name</th>
                                        <th>Purpose</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    // Fetch user bookings from the database
                                    $query = "SELECT b.id, b.user_id, u.name, u.phone, u.email, s.space_name, b.date, b.timeslot, b.coorname, b.purpose, b.status
                                              FROM bookings b
                                              JOIN user_db u ON b.user_id = u.id
                                              JOIN ums_learning_spaces s ON b.space_id = s.id";
                                    $result = $conn->query($query);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$row['id']}</td>";
                                            echo "<td>{$row['name']}</td>";
                                            echo "<td>{$row['phone']}</td>";
                                            echo "<td>{$row['email']}</td>";
                                            echo "<td>{$row['space_name']}</td>";
                                            echo "<td>{$row['date']}</td>";
                                            echo "<td>{$row['timeslot']}</td>";
                                            echo "<td>{$row['coorname']}</td>";
                                            echo "<td>{$row['purpose']}</td>";
                                           // Display different colors for different statuses
                                            $statusColor = '';
                                            switch ($row['status']) {
                                                case 'Approved':
                                                    $statusColor = 'text-success';
                                                    break;
                                                case 'Rejected':
                                                    $statusColor = 'text-danger';
                                                    break;
                                                case 'Pending':
                                                    $statusColor = 'text-secondary';
                                                    break;
                                                default:
                                                    $statusColor = 'text-secondary';
                                            }

                                            echo "<td class='$statusColor'>{$row['status']}</td>";
                                            echo "<td>
                                                    <a href='approve_booking.php?id={$row['id']}' class='btn btn-success'>Approve</a>
                                                    <a href='reject_booking.php?id={$row['id']}' class='btn btn-danger'>Reject</a>
                                                  </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='11'>No bookings found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Table for Tool Bookings -->
                <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Tools Bookings</h4>                            
                    </div>
                    <div class="card-body">
                        <table id="example2" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Tool Name</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                // Fetch tool bookings from the database with user details and tool name
                                $queryTools = "SELECT tb.id, u.name as user_name, u.phone as user_phone, u.email as user_email, t.tool_name, tb.date, tb.status
                                FROM tools_bookings tb
                                JOIN user_db u ON tb.user_id = u.id
                                JOIN tools t ON tb.tool_id = t.id";
                                $resultTools = $conn->query($queryTools);

                                if ($resultTools->num_rows > 0) {
                                    while ($rowTool = $resultTools->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>{$rowTool['id']}</td>";
                                        echo "<td>{$rowTool['user_name']}</td>";
                                        echo "<td>{$rowTool['user_phone']}</td>";
                                        echo "<td>{$rowTool['user_email']}</td>";
                                        echo "<td>{$rowTool['tool_name']}</td>";
                                        echo "<td>{$rowTool['date']}</td>";
                                        // Display different colors for different statuses
                                        $statusColorTool = '';
                                        switch ($rowTool['status']) {
                                            case 'Approved':
                                                $statusColorTool = 'text-success';
                                                break;
                                            case 'Rejected':
                                                $statusColorTool = 'text-danger';
                                                break;
                                            case 'Pending':
                                                $statusColorTool = 'text-secondary';
                                                break;
                                            default:
                                                $statusColorTool = 'text-secondary';
                                        }

                                        echo "<td class='$statusColorTool'>{$rowTool['status']}</td>";
                                        echo "<td>
                                                <a href='approve_tool_booking.php?id={$rowTool['id']}' class='btn btn-success'>Approve</a>
                                                <a href='reject_tool_booking.php?id={$rowTool['id']}' class='btn btn-danger'>Reject</a>
                                                </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No tool bookings found</td></tr>";
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </section>   
</div>
</body>
</html>
<?php
    include('inc/scripts.php');
?>