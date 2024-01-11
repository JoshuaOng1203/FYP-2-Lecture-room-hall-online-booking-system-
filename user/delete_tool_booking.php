<?php
@include 'config.php';

if (isset($_GET['id'])) {
    $toolBookingId = $_GET['id'];

    // Delete the tool booking from the database
    $deleteQuery = "DELETE FROM tools_bookings WHERE id = ?";
    
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $toolBookingId);

    if ($stmt->execute()) {
        $stmt->close();
        header('Location: mybooking.php');
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Invalid request.";
}
?>
