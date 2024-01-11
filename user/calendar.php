<?php
include ('config.php');
session_start();

function build_calendar($month, $year, $spaceId) {
    $daysOfWeek = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
    
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    
    $numberDays = date('t', $firstDayOfMonth);
    
    $dateComponents = getdate($firstDayOfMonth);
    
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];
    
    $dateToday = date('Y-m-d');

    // Create the table tag opener and day headers
    $prev_month = date('m', mktime(0,0,0, $month-1, 1, $year));
    $prev_year = date('Y', mktime(0,0,0, $month-1, 1, $year));
    $next_month = date('m', mktime(0,0,0, $month+1, 1, $year));
    $next_year = date('Y', mktime(0,0,0, $month+1, 1, $year));

    // Build the URL parameters based on whether spaceId is present
    $urlParams = "";
    if (!empty($spaceId)) {
        $urlParams = "&spaceId=".$spaceId;
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
            // Include spaceId in the URL for each day's booking link
            $calendar .= "<td class='$today'><h4>$currentDay</h4> <a href='book.php?date=".$date."&spaceId=".$spaceId."' class='btn btn-success btn-xs'>Book</a>";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <style>
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
    <?php require('inc/links.php'); ?>
</head>

<body>
<?php require('inc/header.php'); ?>
    <div class="container" style="margin-top: 20px;">
        <div class="row">
            <div class="col-md-12">
                <button onclick="goToSpacePage()" class="btn btn-secondary">Back to Learning Space Page</button>
                <?php
                    $dateComponents = getdate();
                    $spaceId = isset($_GET['spaceId']) ? $_GET['spaceId'] : '';
                    if (isset($_GET['month']) && isset($_GET['year'])){
                        $month = $_GET['month'];                 
                        $year = $_GET['year'];
                    } else {
                        $month = $dateComponents['mon'];                 
                        $year = $dateComponents['year'];
                    }
                    echo build_calendar($month, $year, $spaceId);
                ?>
            </div>
        </div>
    </div>
    <script>
        function goToSpacePage() {
            window.location.href = 'spaces.php';
        }
    </script>
</body>
</html>