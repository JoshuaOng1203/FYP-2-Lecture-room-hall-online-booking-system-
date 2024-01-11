<?php
// Include your configuration file
@include 'config.php';
session_start();

// Check if the AJAX request is for fetching tool data
if (isset($_GET['action']) && $_GET['action'] == 'get_tool_data' && isset($_GET['id'])) {
    $toolId = mysqli_real_escape_string($conn, $_GET['id']);

    // Fetch tool data from the database
    $query = "SELECT * FROM tools WHERE id = $toolId";
    $result = mysqli_query($conn, $query);

    if ($tool = mysqli_fetch_assoc($result)) {
        // Return data in JSON format
        echo json_encode($tool);
    } else {
        // Handle error if tool data is not found
        echo json_encode(['error' => 'Tool data not found']);
    }

    exit;
}

// Check if the form for adding a tool is submitted
if (isset($_POST['add_tool'])) {
    // Sanitize and escape input data
    $toolName = mysqli_real_escape_string($conn, $_POST['name']);

    // Generate a unique file name based on tool name
    $imagePath = 'images/tools/' . $toolName . '.jpg';

    // Check if an image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tempFile = $_FILES['image']['tmp_name'];
        if (move_uploaded_file($tempFile, $imagePath)) {
            // File uploaded successfully
        } else {
            echo "Error uploading image.";
        }
    } else {
        echo "File upload failed.";
    }

    // Insert data into the database using prepared statement
    $insertQuery = "INSERT INTO tools (tool_name, image) VALUES (?, ?)";

    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "ss", $toolName, $imagePath);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: tool_management.php');
        exit;
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}

// Check if the form for editing a tool is submitted
if (isset($_POST['edit_tool'])) {
    // Sanitize and escape input data
    $toolId = mysqli_real_escape_string($conn, $_POST['tool_id']);
    $toolName = mysqli_real_escape_string($conn, $_POST['name']);

    // Get the original image filename
    $originalImageFilename = mysqli_real_escape_string($conn, $_POST['edit_original_image_filename']);

    // Check if a new image was uploaded
    $imagePath = 'images/tools/' . $toolName . '.jpg'; // default to empty, meaning no new image

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tempFile = $_FILES['image']['tmp_name'];
        $imagePath = 'images/tools/' . $toolName . '.jpg';
        if (!move_uploaded_file($tempFile, $imagePath)) {
            echo "Error uploading image.";
            exit;
        }
    } else {
        // Use the original image filename if no new image is uploaded
        $imagePath = $originalImageFilename;
    }

    // Update data in the database using prepared statement
    $updateQuery = "UPDATE tools SET tool_name=?, image=? WHERE id=?";

    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "ssi", $toolName, $imagePath, $toolId);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: tool_management.php');
        exit;
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}

// Check if the request is for deleting a tool
if (isset($_GET['action']) && $_GET['action'] == 'delete_tool' && isset($_GET['id'])) {
    $toolId = mysqli_real_escape_string($conn, $_GET['id']);

    // Delete the tool from the database
    $deleteQuery = "DELETE FROM tools WHERE id = $toolId";

    if (mysqli_query($conn, $deleteQuery)) {
        header('Location: tool_management.php');
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
