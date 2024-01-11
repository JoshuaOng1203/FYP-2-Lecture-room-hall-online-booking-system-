<?php
@include 'config.php';

if (isset($_GET['id'])) {
    $spaceId = $_GET['id'];

    // Fetch the image path before deleting from the database
    $getImagePathQuery = "SELECT image FROM ums_learning_spaces WHERE id = $spaceId";
    $result = mysqli_query($conn, $getImagePathQuery);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $imagePath = 'images/learning_spaces/' . basename($row['image']);

        // Attempt to delete the learning space from the database
        $deleteQuery = "DELETE FROM ums_learning_spaces WHERE id = $spaceId";

        if (mysqli_query($conn, $deleteQuery)) {
            // Attempt to delete the image file from the directory
            if (file_exists($imagePath) && is_writable($imagePath)) {
                if (unlink($imagePath)) {
                    echo 'Learning space and image file deleted successfully.';
                } else {
                    echo 'Error deleting image file.';
                }
            } else {
                echo 'Image file not found or not writable.';
            }

            header('Location: learning_space_management.php');
            exit;
        } else {
            echo 'Error deleting learning space: ' . mysqli_error($conn);
        }
    } else {
        echo 'Error fetching image path: ' . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
