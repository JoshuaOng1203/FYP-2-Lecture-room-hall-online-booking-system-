<?php
@include 'config.php';

session_start();
$space_name = "";
$start_time = "";
$end_time = "";

if (isset($_SESSION['user_name'])) {
    $user_name = $_SESSION['user_name'];
}

if (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];
}

if (isset($_SESSION['user_phone'])) {
    $user_phone = $_SESSION['user_phone'];
}

// Function to fetch space information by spaceId
function getSpaceInfoById($conn, $spaceId)
{
    $spaceQuery = "SELECT space_name, start_time, end_time FROM ums_learning_spaces WHERE id = ?";
    $spaceStmt = $conn->prepare($spaceQuery);
    $spaceStmt->bind_param('i', $spaceId);

    if ($spaceStmt->execute()) {
        $spaceResult = $spaceStmt->get_result();

        if ($spaceResult->num_rows > 0) {
            $spaceRow = $spaceResult->fetch_assoc();
            return $spaceRow;
        }
    }

    $spaceStmt->close();
    return null;
}

// Function to fetch booked timeslots by date, spaceId, and status
function getBookedTimeSlots($conn, $date, $spaceId)
{
    $stmt = $conn->prepare("SELECT timeslot, status FROM bookings WHERE date = ? AND space_id = ?");
    $stmt->bind_param('si', $date, $spaceId);
    $bookedSlots = [];

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bookedSlots[$row['timeslot']] = $row['status'];
            }
        }
        $stmt->close();
    }

    return $bookedSlots;
}

// Function to insert a booking
function insertBooking($conn, $user_id, $coorname, $purpose, $date, $timeslot, $space_id)
{
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, coorname, purpose, date, timeslot, space_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('issssi', $user_id, $coorname, $purpose, $date, $timeslot, $space_id);
    $stmt->execute();
    $stmt->close();
}

// Check if spaceId and date are set in the URL parameters
if (isset($_GET['spaceId'])) {
    $spaceId = $_GET['spaceId'];

    // Fetch space information from the database based on $spaceId
    $spaceInfo = getSpaceInfoById($conn, $spaceId);

    if ($spaceInfo) {
        $space_name = $spaceInfo['space_name'];
        $start_time = $spaceInfo['start_time'];
        $end_time = $spaceInfo['end_time'];

        // Generate time slots
        $duration = 120;
        $cleanup = 0;
        $timeslots = timeslots($duration, $cleanup, $start_time, $end_time);

        // Use $timeslots in your HTML code
    } else {
        // Handle the case where the space doesn't exist
        echo "Invalid space.";
        exit();
    }
}

if (isset($_GET['date'])) {
    $date = $_GET['date'];

    // Fetch booked timeslots for each status
    $bookedTimeslots = getBookedTimeSlots($conn, $date, $spaceId);

    $stmt = $conn->prepare("SELECT * FROM bookings WHERE date = ? AND space_id = ?");
    $stmt->bind_param('si', $date, $spaceId);
    $bookings = [];

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row;
            }
            $stmt->close();
        }
    }
}

if (isset($_POST['submit'])) {
    $coorname = $_POST['coorname'];
    $purpose = $_POST['purpose'];
    $timeslot = $_POST['timeslot'];
    $space_id = $_GET['spaceId']; // Assuming spaceId is set in the URL

    // Check if the timeslot is already booked
    if (!array_key_exists($timeslot, $bookedTimeslots)) {
        // Use user_id from session directly
        $user_id = $_SESSION['user_id'];

        // Insert the booking
        insertBooking($conn, $user_id, $coorname, $purpose, $date, $timeslot, $space_id);

        $msg = "<div class='alert alert-success'>Booking Successful</div>";
        $bookedTimeslots[$timeslot] = 'Pending';
    } else {
        $msg = "<div class='alert alert-danger'>Timeslot already booked. Please choose another timeslot.</div>";
    }
}

$duration = 120;
$cleanup = 0;

function timeslots($duration, $cleanup, $start, $end)
{
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT" . $duration . "M");
    $cleanupInterval = new DateInterval("PT" . $cleanup . "M");
    $slots = array();

    for ($intStart = $start; $intStart < $end; $intStart->add($interval)->add($cleanupInterval)) {
        $endPeriod = clone $intStart;
        $endPeriod->add($interval);
        if ($endPeriod > $end) {
            break;
        }

        $slots[] = $intStart->format("H:iA") . "-" . $endPeriod->format("H:iA");
    }

    return $slots;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Page</title>
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <style>
        /* Style the specific read-only input field for the phone number */
        input[name="user_phone"].readonly {
            background-color: #fff;
            color: #000;
            border: 1px solid #ccc;
        }
        
        .close {
        font-size: 24px !important; 
        padding: 10px 15px !important;
        background-color: transparent !important;
        border: none !important;
        }

        .timeslot-button {
            margin-bottom: 10px;
            /* You can adjust the value to control the vertical spacing. */
        }
    </style>
    <?php require('inc/links.php'); ?>
</head>

<body>
    <?php require('inc/header.php'); ?>
    <div class="container" style="margin-top: 20px;">
        <button onclick="goToSpacePage()" class="btn btn-secondary">Back to space page</button>
        <h1 class="text-center" style="font-size: 30px;">Book for Date: <?php echo date('m/d/Y', strtotime($date)); ?></h1>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <?php echo (isset($msg)) ? $msg : ""; ?>
            </div>
            <?php
            foreach ($timeslots as $ts) {
                $isBooked = array_key_exists($ts, $bookedTimeslots);
                $status = $isBooked ? $bookedTimeslots[$ts] : null;
            ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <?php if ($isBooked) { ?>
                            <?php if ($status === 'Approved') { ?>
                                <button class="btn btn-danger" disabled>
                                    <?php echo $ts; ?>
                                </button>
                            <?php } elseif ($status === 'Rejected') { ?>
                                <button class="btn btn-success book" data-timeslot="<?php echo $ts; ?>">
                                    <?php echo $ts; ?>
                                </button>
                            <?php } else { ?>
                                <button class="btn btn-secondary" disabled>
                                    <?php echo $ts; ?>
                                </button>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="timeslot-button">
                                <button class="btn btn-success book" data-timeslot="<?php echo $ts; ?>">
                                    <?php echo $ts; ?>
                                </button>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Modal content -->
    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" style="text-align: left;">Booking: <span id="slot"></span></h4>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="">Name</label>
                                    <input type="text" name="user_name" value="<?php echo $user_name; ?>" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <input type="email" name="user_email" value="<?php echo $user_email; ?>" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="">Phone</label>
                                    <input type="number" name="user_phone" value="<?php echo $user_phone; ?>" class="form-control readonly" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="">Space Name</label>
                                    <input type="text" name="space_name" value="<?php echo $space_name; ?>" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="">Timeslot</label>
                                    <input type="text" name="timeslot" id="timeslot" class="form-control" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="">Coordinator Name</label>
                                    <input required type="text" name="coorname" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Purpose</label>
                                    <input required type="text" name="purpose" class="form-control">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(".book").click(function() {
            var timeslot = $(this).data('timeslot');
            $("#slot").html(timeslot);
            $("#timeslot").val(timeslot);
            $("#myModal").modal("show");
        });
    </script>
    
    <script>
        function goToSpacePage() {
            window.location.href = 'spaces.php';
        }
    </script>
</body>

</html>
