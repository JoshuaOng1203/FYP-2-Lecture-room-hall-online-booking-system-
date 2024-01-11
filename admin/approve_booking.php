<?php
@include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $bookingId = $_GET['id'];

    // Update the booking status to 'Approved'
    $updateQuery = "UPDATE bookings SET status = 'Approved' WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('i', $bookingId);

    if ($stmt->execute()) {
        // Success: Redirect back to the admin page or wherever you need
        header("Location: user_bookings.php");
        exit();
    } else {
        // Error handling
        echo "Error updating booking status: " . $stmt->error;
    }

    $stmt->close();
} else {
    // Invalid request
    header("Location: admin_bookings.php");
    exit();
}
?>
