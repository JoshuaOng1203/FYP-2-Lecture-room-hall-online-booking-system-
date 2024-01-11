<?php
@include 'config.php';
session_start();

if (isset($_GET['id'])) {
    $toolBookingId = $_GET['id'];

    // Update the status of the tool booking to 'Rejected' in the database
    $updateQuery = "UPDATE tools_bookings SET status = 'Rejected' WHERE id = ?";
    
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('i', $toolBookingId);

    if ($stmt->execute()) {
        $stmt->close();
        header('Location: user_bookings.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>
