<?php

@include 'config.php';

// id | space_name | building | location | capacity | equipments | start_time | end_time | image
$query = "SELECT space_name, COUNT(*) as total_bookings 
          FROM ums_learning_spaces ls
          JOIN bookings b ON ls.id = b.space_id
          WHERE ls.building = ? AND b.date >= ? AND b.status = 'Approved'
          GROUP BY space_name";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("ss", $building, $formattedStartDate);
    $building = $_GET['building'];
    $formattedStartDate = $_GET['date'];
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();

    while ($row = $result->fetch_assoc()) {
        $data[] = array(
            $row['space_name'],
            $row['total_bookings'],
        );
    }

    $stmt->close();

    // Return data as JSON
    echo json_encode(['data' => $data]);
} else {
    echo json_encode(['error' => 'Database error']);
}

$conn->close();
?>
