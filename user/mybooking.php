<?php
@include 'config.php';
session_start();

// Fetch user ID from the session
$userId = $_SESSION['user_id']; 

$query = "SELECT b.id, b.coorname, b.purpose, s.space_name, b.date, b.timeslot, b.status FROM bookings b
          JOIN ums_learning_spaces s ON b.space_id = s.id
          WHERE b.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $userId);

$userBookings = [];

if ($stmt->execute()) {
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $userBookings[] = $row;
    }

    $stmt->close();
}

// Query to fetch tool bookings
$queryToolBookings = "SELECT tb.id, tb.user_id, tb.tool_id, tb.date, tb.status, t.tool_name FROM tools_bookings tb
                      JOIN tools t ON tb.tool_id = t.id
                      WHERE tb.user_id = ?";
$stmtToolBookings = $conn->prepare($queryToolBookings);
$stmtToolBookings->bind_param('s', $userId);

$userToolBookings = [];

if ($stmtToolBookings->execute()) {
    $resultToolBookings = $stmtToolBookings->get_result();

    while ($rowToolBookings = $resultToolBookings->fetch_assoc()) {
        $userToolBookings[] = $rowToolBookings;
    }

    $stmtToolBookings->close();
}
function getStatusColorClass($status) {
    switch ($status) {
        case 'Approved':
            return 'text-success'; // Assuming you have a CSS class for green text
        case 'Rejected':
            return 'text-danger'; // Assuming you have a CSS class for red text
        case 'Pending':
            return 'text-secondary'; // Assuming you have a CSS class for grey text
        default:
            return 'text-secondary';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Booking Website</title>
    <?php require('inc/links.php'); ?>
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <style>
        .close {
            font-size: 24px !important; 
            padding: 10px 15px !important;
            background-color: transparent !important;
            border: none !important;
            }
    </style>
</head>

<body>

    <?php require('inc/header.php'); ?>

    <div class="my-5 px-4">
        <h2 class="fw-bold text-center">My Bookings</h2>
        <div class="h-line bg-dark"></div>
    </div>

    <section class="container-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 mb-5">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Learning Space Bookings</h5>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Coordinator Name</th>
                                        <th>Purpose of Booking</th>
                                        <th>Space Name</th>
                                        <th>Date</th>
                                        <th>Time Slot</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userBookings as $booking) : ?>
                                        <tr>
                                            <td><?= $booking['id']; ?></td>
                                            <td><?= $booking['coorname']; ?></td>
                                            <td><?= $booking['purpose']; ?></td>
                                            <td><?= $booking['space_name']; ?></td>
                                            <td><?= $booking['date']; ?></td>
                                            <td><?= $booking['timeslot']; ?></td>
                                            <td class="<?= getStatusColorClass($booking['status']); ?>"><?= $booking['status']; ?></td>
                                            <td>
                                                <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteBookingModal<?= $booking['id']; ?>" onclick="deleteBooking(<?= $booking['id']; ?>)">Cancel</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Tool Bookings Table -->
                <div class="col-md-12 mb-5">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tool Bookings</h5>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tool Name</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userToolBookings as $toolBooking) : ?>
                                        <tr>
                                            <td><?= $toolBooking['id']; ?></td>
                                            <td><?= $toolBooking['tool_name']; ?></td>
                                            <td><?= $toolBooking['date']; ?></td>
                                            <td class="<?= getStatusColorClass($toolBooking['status']); ?>"><?= $toolBooking['status']; ?></td>
                                            <td>
                                                <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteToolBookingModal<?= $toolBooking['id']; ?>" onclick="deleteToolBooking(<?= $toolBooking['id']; ?>)">Cancel</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <?php foreach ($userBookings as $booking) : ?>
        <div class="modal fade" id="deleteBookingModal<?= $booking['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteBookingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteBookingModalLabel">Cancel Confirmation</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to cancel this booking?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <a href="delete_booking.php?id=<?= $booking['id']; ?>" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Tool Booking Modals -->
    <?php foreach ($userToolBookings as $toolBooking) : ?>
        <div class="modal fade" id="deleteToolBookingModal<?= $toolBooking['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteToolBookingModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteBookingModalLabel">Cancel Confirmation</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to cancel this booking?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <a href="delete_tool_booking.php?id=<?= $toolBooking['id']; ?>" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        function deleteBooking(bookingId) {
            // Use JavaScript to prevent the default behavior of the anchor link
            event.preventDefault();

            // Open the corresponding modal
            $('#deleteBookingModal' + bookingId).modal('show');
        }

        function deleteToolBooking(toolBookingId) {
            // Use JavaScript to prevent the default behavior of the anchor link
            event.preventDefault();

            // Open the corresponding modal for tool bookings
            $('#deleteToolBookingModal' + toolBookingId).modal('show');
        }
    </script>

    <?php require('inc/footer.php'); ?>
</body>

</html>
