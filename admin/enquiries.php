<?php
@include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_as_read'])) {
    $id_to_mark_as_read = $_POST['mark_as_read'];

    // Update the database to mark the entry as read
    $updateQuery = "UPDATE user_queries SET seen = 1 WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param('i', $id_to_mark_as_read);

    if ($stmt->execute()) {
        // Reload the page after marking as read
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        echo 'Error marking as read';
    }

    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id_to_delete = $_POST['delete'];

    // Delete the entry from the database
    $deleteQuery = "DELETE FROM user_queries WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $id_to_delete);

    if ($stmt->execute()) {
        // Reload the page after deletion
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        echo 'Error deleting entry';
    }

    $stmt->close();
}
?>

<?php 
    include('inc/header.php');
    include('inc/topbar.php');
    include('inc/sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Enquiries Management Page</title>
  
</head>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="container-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header">
                            <h4>Users Enquiries</h4>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM user_queries";
                                    $result = mysqli_query($conn, $query);

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td>' . $row['id'] . '</td>';
                                        echo '<td>' . $row['name'] . '</td>';
                                        echo '<td>' . $row['email'] . '</td>';
                                        echo '<td>' . $row['subject'] . '</td>';
                                        echo '<td>' . $row['message'] . '</td>';
                                        echo '<td>' . $row['date'] . '</td>';
                                        echo '<td>';
                                        if ($row['seen'] == 0) {
                                            echo '<form method="post" style="display:inline;">';
                                            echo '<input type="hidden" name="mark_as_read" value="' . $row['id'] . '">';
                                            echo '<button type="submit" class="btn btn-success">Mark as Read</button>';
                                            echo '</form>';
                                        }
                                        echo '<form method="post" style="display:inline;">';
                                        echo '<input type="hidden" name="delete" value="' . $row['id'] . '">';
                                        echo '<button type="submit" class="btn btn-danger">Delete</button>';
                                        echo '</form>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>   
</div>
</body>
</html>
<?php
    include('inc/scripts.php');
?>
