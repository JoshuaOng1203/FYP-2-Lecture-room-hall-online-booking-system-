<?php
@include 'config.php';

if (isset($_GET['id'])) {
    $toolId = $_GET['id'];

    // Fetch the image path before deleting from the database
    $getImagePathQuery = "SELECT image FROM tools WHERE id = $toolId";
    $result = mysqli_query($conn, $getImagePathQuery);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        $imagePath = 'images/tools/' . basename($row['image']);

        // Attempt to delete the tool from the database
        $deleteQuery = "DELETE FROM tools WHERE id = $toolId";

        if (mysqli_query($conn, $deleteQuery)) {
            // Attempt to delete the image file from the directory
            if (file_exists($imagePath) && is_writable($imagePath)) {
                if (unlink($imagePath)) {
                    echo 'Tool and image file deleted successfully.';
                } else {
                    echo 'Error deleting image file.';
                }
            } else {
                echo 'Image file not found or not writable.';
            }

            header('Location: tool_management.php');
            exit;
        } else {
            echo 'Error deleting tool: ' . mysqli_error($conn);
        }
    } else {
        echo 'Error fetching image path: ' . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
