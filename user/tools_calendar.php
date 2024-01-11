<?php
@include 'config.php';
session_start();

// Fetch tool data from the database
$query = "SELECT `id`, `tool_name`, `image` FROM `tools` WHERE 1";
$result = mysqli_query($conn, $query);

$tools = array();

while ($toolData = mysqli_fetch_assoc($result)) {
    $tools[] = $toolData;
}

function getUserDetails($userId, $conn) {
    $userDetails = array();

    $userQuery = "SELECT `name`, `phone`, `email` FROM `user_db` WHERE `id` = $userId";
    $userResult = mysqli_query($conn, $userQuery);

    if ($userResult && $userData = mysqli_fetch_assoc($userResult)) {
        $userDetails = array(
            'name' => $userData['name'],
            'email' => $userData['email'],
            'phone' => $userData['phone']
        );
    }

    return $userDetails;
}

function build_calendar($month, $year, $toolId) {
    global $conn;

    // Create array containing abbreviations of days of the week.
    $daysOfWeek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

    // What is the first day of the month in question?
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);

    // How many days does this month contain?
    $numberDays = date('t', $firstDayOfMonth);

    // Retrieve some information about the first day of the month in question.
    $dateComponents = getdate($firstDayOfMonth);

    // What is the name of the month in question?
    $monthName = $dateComponents['month'];

    // What is the index value (0-6) of the first day of the month in question?
    $dayOfWeek = $dateComponents['wday'];

    $dateToday = date('Y-m-d');

    // Create the table tag opener and day headers
    $prev_month = date('m', mktime(0,0,0, $month-1, 1, $year));
    $prev_year = date('Y', mktime(0,0,0, $month-1, 1, $year));
    $next_month = date('m', mktime(0,0,0, $month+1, 1, $year));
    $next_year = date('Y', mktime(0,0,0, $month+1, 1, $year));

    // Build the URL parameters based on whether toolId is present
    $urlParams = "";
    if (!empty($toolId)) {
        $urlParams = "&toolId=".$toolId;
    }

    $calendar = "<center><h2>$monthName $year</h2>";
    $calendar .="<a class='btn btn-primary btn-xs' href='?month=".$prev_month."&year=".$prev_year.$urlParams."'>Prev Month</a>";
    $calendar .="<a class='btn btn-primary btn-xs' href='?month=".date('m')."&year=".date('Y').$urlParams."'>Current Month</a>";
    $calendar .="<a class='btn btn-primary btn-xs' href='?month=".$next_month."&year=".$next_year.$urlParams."'>Next Month</a></center>&nbsp;";
    $calendar .="<table class='table table-bordered'>";
    $calendar .= "<tr>";

    // Create the calendar headers
    foreach($daysOfWeek as $day) {
        $calendar .= "<th  class='header'>$day</th>";
    }

    $calendar .= "</tr><tr>";

    // Create the rest of the calendar
    // Initiate the day counter, starting with the 1st.
    $currentDay = 1;

    // The variable $dayOfWeek is used to ensure that the calendar display consists of exactly 7 columns.
    if ($dayOfWeek > 0) { 
        for($k = 0; $k < $dayOfWeek; $k++){
            $calendar .= "<td class='empty'></td>"; 
        }
    }
    
    $month = str_pad($month, 2, "0", STR_PAD_LEFT);
    
    while ($currentDay <= $numberDays) {
        // Seventh column (Saturday) reached. Start a new row.
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }
        
        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";
        $dayname = strtolower(date('l', strtotime($date)));
        $today = $date == date('Y-m-d') ? "today" : "";
        
        if ($date < date('Y-m-d')) {
            $calendar .= "<td><h4>$currentDay</h4> <button class='btn btn-danger btn-xs'>N/A</button>";
        } else {
            // Include toolId in the URL for each day's booking link
            $toolName = getToolNameById($toolId, $conn); // Fetch the tool name
            $bookingStatus = getBookingStatus($toolId, $date, $conn);
            $calendar .= "<td class='$today'><h4>$currentDay</h4>";

            // Check booking status and display appropriate button
            if ($bookingStatus == 'Pending') {
                $calendar .= "<button class='btn btn-secondary btn-xs'>Pending Approval</button>";
            } elseif ($bookingStatus == 'Approved') {
                $calendar .= "<button class='btn btn-danger btn-xs'>Booked</button>";
            } else {
                $calendar .= "<a class='btn btn-success btn-xs' data-date='$date' data-tool-name='$toolName' data-tool-id='$toolId'>Book</a>";
            }
            $calendar .= "</td>";
        }

        // Increment counters
        $currentDay++;
        $dayOfWeek++;
    }

    // Complete the row of the last week in the month, if necessary
    if ($dayOfWeek < 7) { 
        $remainingDays = 7 - $dayOfWeek;
        for ($i = 0; $i < $remainingDays; $i++){
            $calendar .= "<td class='empty'></td>"; 
        }
    }
    
    $calendar .= "</tr></table>";
    return $calendar;
}

// Function to get booking status for a specific tool on a given date
function getBookingStatus($toolId, $date, $conn) {
    $status = 'Available'; // Default status is available

    $query = "SELECT status FROM tools_bookings WHERE tool_id = '$toolId' AND date = '$date'";
    $result = mysqli_query($conn, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $status = $row['status'];
    }

    return $status;
}
// Function to get tool name by ID
function getToolNameById($toolId, $conn) {
    $toolName = "";
    $query = "SELECT tool_name FROM tools WHERE id = '$toolId'";
    $result = mysqli_query($conn, $query);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $toolName = $row['tool_name'];
    }

    return $toolName;
}

function getBookedDates($toolId, $conn) {
    $bookedDates = array();
    if (!empty($toolId)) {
        $query = "SELECT date FROM tools_bookings WHERE tool_id = '$toolId'";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            $bookedDates[] = $row['date'];
        }
    }
    return $bookedDates;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bookingDate = $_POST['booking_date'];
    $toolId = $_POST['tool_id'] ?? null;

    // Retrieve user ID from the session
    $userId = $_SESSION['user_id'];

    // Insert the booking into the database with status set to 'Pending'
    $query = "INSERT INTO tools_bookings (user_id, tool_id, date, status) VALUES ('$userId', '$toolId', '$bookingDate', 'Pending')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Booking successful
        $bookingMessage = "Booking is pending approval!";
    } else {
        // Booking failed
        $bookingMessage = "Booking failed. Please try again.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tool Calendar</title>
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* CSS styles here */
        @media only screen and (max-width: 760px), (min-device-width: 802px) and (max-device-width: 1020px){
            table, thead, tbody, th, td, tr {
                display: block;
            }

            .empty{
                display: none;
            }

            th{
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr{
                border: 1px solid #ccc;
            }

            td{
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
            }
            /* Label the data*/
            td:nth-of-type(1):before{
                content: "Sunday";
            }

            td:nth-of-type(2):before{
                content: "Monday";
            }

            td:nth-of-type(3):before{
                content: "Tuesday";
            }

            td:nth-of-type(4):before{
                content: "Wednesday";
            }

            td:nth-of-type(5):before{
                content: "Thursday";
            }

            td:nth-of-type(6):before{
                content: "Friday";
            }

            td:nth-of-type(7):before{
                content: "Saturday";
            }
        }

        /*Smartphones (potrait and landscape) */
        @media only screen and (min-device-width: 320px) and (max-device-width: 480px){
            body{
                padding: 0;
                margin: 0;
            }
        }

        /* Ipads (potrait and landscape)*/
        @media only screen and (min-device-width: 802px) and (max-device-width: 1020px){
            body{
                width: 495px;
            }
        }

        @media (min-width:641px){
            table{
                table-layout: fixed;
            }

            td{
                width: 33%;
            }
        }

        .row{
            margin-top: 20px;
        }

        .today{
            background: yellow;
        }

        .btn {
            margin-right: 10px; /* Adjust the value as needed to control the spacing between the buttons */
        }
        td h4 {
            font-size: 20px; /* You can adjust the font size as needed */
        }
        .btn.btn-success.btn-xs {
            font-size: 12px; /* Adjust the font size as needed */
            padding: 5px 10px; /* Adjust the padding as needed */
        }
        .btn.btn-danger.btn-xs {
            font-size: 12px; /* Adjust the font size as needed */
            padding: 5px 10px; /* Adjust the padding as needed */
        }
    </style>
    <style>
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
    </style>
    <?php require('inc/links.php'); ?>
</head>
<body>
    <?php require('inc/header.php'); ?>

    <div class="container" style="margin-top: 20px;">
        <button onclick="goToSpacePage()" class="btn btn-secondary">Back to tool page</button>
        <div class="row">
                <?php
                // Output the booking message if it exists
                if (isset($bookingMessage)) {
                    echo '<div class="alert alert-success" role="alert">' . $bookingMessage . '</div>';
                }
                $dateComponents = getdate();
                $toolId = isset($_GET['toolId']) ? $_GET['toolId'] : '';
                if (isset($_GET['month']) && isset($_GET['year'])) {
                    $month = $_GET['month'];
                    $year = $_GET['year'];
                } else {
                    $month = $dateComponents['mon'];
                    $year = $dateComponents['year'];
                }
                echo build_calendar($month, $year, $toolId);
                ?>
            </div>
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
                            <?php
                            // Retrieve user ID from the session
                            $userId = $_SESSION['user_id'];
                            $userQuery = "SELECT `name`, `phone`, `email` FROM `user_db` WHERE `id` = $userId";
                            $userResult = mysqli_query($conn, $userQuery);

                            if ($userResult && $userData = mysqli_fetch_assoc($userResult)) {
                                $user_name = $userData['name'];
                                $user_email = $userData['email'];
                                $user_phone = $userData['phone'];
                            }
                            ?>
                            <div class="form-group">
                                <input type="hidden" name="tool_id" id="tool_id" class="form-control readonly" readonly>
                            </div>
                            <div class="form-group">
                                <label for="">Name</label>
                                <input type="text" name="user_name" value="<?php echo isset($user_name) ? $user_name : ''; ?>" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="">Email</label>
                                <input type="email" name="user_email" value="<?php echo isset($user_email) ? $user_email : ''; ?>" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="">Phone</label>
                                <input type="number" name="user_phone" value="<?php echo isset($user_phone) ? $user_phone : ''; ?>" class="form-control readonly" readonly>
                            </div>
                            <div class="form-group">
                                <label for="">Tool Name</label>
                                <input type="text" name="tool_name" id="tool_name" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="">Date</label>
                                <input type="text" name="booking_date" id="booking_date" class="form-control" readonly>
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

<?php require('inc/footer.php'); ?>

<script>
    $(document).ready(function () {
    console.log('Script is running!');

    // Update modal fields when the "Book" button is clicked
    $(document).on('click', '.btn-success', function (event) {
        console.log('Book button clicked!');
        var button = $(event.currentTarget);
        var date = button.data('date');
        var toolName = button.data('tool-name');
        var toolId = button.data('tool-id');
        console.log('Date:', date, 'Tool Name:', toolName, 'Tool ID:', toolId);

        // Set the modal fields
        var modal = $('#myModal');
        modal.find('#booking_date').val(date);
        modal.find('#tool_name').val(toolName);
        modal.find('#tool_id').val(toolId);

        // Change the button appearance to grey (Pending)
        button.removeClass('btn-success').addClass('btn-secondary').text('Pending Approval');

        // Show the modal
        modal.modal('show');
    });
    $('#myModal').on('hidden.bs.modal', function () {
    location.reload();
    });
});

</script>
<script>
    function goToSpacePage() {
        window.location.href = 'tools.php';
    }
</script>

</body>

</html>

