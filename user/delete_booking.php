<?php
@include 'config.php';

if (isset($_GET['id'])) {
    $bookingId = $_GET['id'];

    // Delete the booking from the database
    $deleteQuery = "DELETE FROM bookings WHERE id = ?";
    
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $bookingId);

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
