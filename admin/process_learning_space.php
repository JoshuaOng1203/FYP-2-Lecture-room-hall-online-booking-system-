<?php
// Include your configuration file
@include 'config.php';
session_start();

// Check if the AJAX request is for fetching space data
if (isset($_GET['action']) && $_GET['action'] == 'get_space_data' && isset($_GET['id'])) {
    $spaceId = mysqli_real_escape_string($conn, $_GET['id']);

    // Fetch learning space data from the database
    $query = "SELECT * FROM ums_learning_spaces WHERE id = $spaceId";
    $result = mysqli_query($conn, $query);

    if ($space = mysqli_fetch_assoc($result)) {
        // Convert the 'equipments' string to an array
        $space['equipments'] = explode(', ', $space['equipments']);

        // Return data in JSON format
        echo json_encode($space);
    } else {
        // Handle error if space data is not found
        echo json_encode(['error' => 'Space data not found']);
    }

    exit;
}

// Check if the form for adding a space is submitted
if (isset($_POST['add_space'])) {
    // Sanitize and escape input data
    $spaceName = mysqli_real_escape_string($conn, $_POST['name']);
    $building = mysqli_real_escape_string($conn, $_POST['building']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $capacity = mysqli_real_escape_string($conn, $_POST['capacity']);
    $equipments = implode(', ', $_POST['equipment']); // Assuming equipment is an array
    $startTime = mysqli_real_escape_string($conn, $_POST['start_time']);
    $endTime = mysqli_real_escape_string($conn, $_POST['end_time']);

    // Generate a unique file name based on space name
    $imagePath = 'images/learning_spaces/' . $spaceName . '.jpg';

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
    $insertQuery = "INSERT INTO ums_learning_spaces (space_name, building, location, capacity, equipments, start_time, end_time, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($stmt, "sssissss", $spaceName, $building, $location, $capacity, $equipments, $startTime, $endTime, $imagePath);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: learning_space_management.php');
        exit;
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}

// Check if the form for editing a space is submitted
if (isset($_POST['edit_space'])) {
    // Sanitize and escape input data
    $spaceId = mysqli_real_escape_string($conn, $_POST['room_id']);
    $spaceName = mysqli_real_escape_string($conn, $_POST['name']);
    $building = mysqli_real_escape_string($conn, $_POST['building']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $capacity = mysqli_real_escape_string($conn, $_POST['capacity']);
    $equipments = implode(', ', $_POST['edit_equipment']); // Assuming equipment is an array
    $startTime = mysqli_real_escape_string($conn, $_POST['edit_start_time']);
    $endTime = mysqli_real_escape_string($conn, $_POST['edit_end_time']);

    // Get the original image filename
    $originalImageFilename = mysqli_real_escape_string($conn, $_POST['edit_original_image_filename']);

    // Check if a new image was uploaded
    $imagePath = 'images/learning_spaces/' . $spaceName . '.jpg'; // default to empty, meaning no new image

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $tempFile = $_FILES['image']['tmp_name'];
        $imagePath = 'images/learning_spaces/' . $spaceName . '.jpg';
        if (!move_uploaded_file($tempFile, $imagePath)) {
            echo "Error uploading image.";
            exit;
        }
    } else {
        // Use the original image filename if no new image is uploaded
        $imagePath = $originalImageFilename;
    }

    // Update data in the database using prepared statement
    $updateQuery = "UPDATE ums_learning_spaces SET space_name=?, building=?, location=?, capacity=?, equipments=?, start_time=?, end_time=?, image=? WHERE id=?";

    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "sssissssi", $spaceName, $building, $location, $capacity, $equipments, $startTime, $endTime, $imagePath, $spaceId);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: learning_space_management.php');
        exit;
    } else {
        echo "Error: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}

?>
