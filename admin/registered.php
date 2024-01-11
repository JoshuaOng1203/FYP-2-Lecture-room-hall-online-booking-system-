<?php
    @include 'config.php';
    session_start();

    // Check if the delete button is clicked
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
        $userId = $_POST['user_id'];

        // Perform the deletion
        $query = "DELETE FROM user_db WHERE id=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();

        // Redirect or display a success message
        header("Location: registered.php");
        exit();
    }

?>

<?php include('inc/header.php'); ?>
<?php include('inc/topbar.php'); ?>
<?php include('inc/sidebar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>User Management Page</title>
  
</head>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="container-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="card">
                        <div class="card-header">
                            <h4>Registered User</h4>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Phone Number</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM user_db";
                                        $result = mysqli_query($conn, $query);

                                        while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['phone']; ?></td>
                                                <td><?php echo $row['email']; ?></td>
                                                <td>
                                                    <form method="post" style="display: inline-block;">
                                                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $row['id']; ?>">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>

                                            <!-- Delete Confirmation Modal -->
                                            <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to delete this user?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <form method="post" style="display: inline-block;">
                                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                                <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</body>
</html>
<?php include('inc/scripts.php'); ?>



