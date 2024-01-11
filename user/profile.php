<?php
@include 'config.php';
session_start();

$msg = '';
$user_id = $_SESSION['user_id'] ?? '';

if (isset($_SESSION['user_name'])) {
    $user_name = $_SESSION['user_name'];
}

if (isset($_SESSION['user_email'])) {
    $user_email = $_SESSION['user_email'];
}

if (isset($_SESSION['user_phone'])) {
    $user_phone = $_SESSION['user_phone'];
}

if (isset($_POST['update_profile'])) {
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name'] ?? '');
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email'] ?? '');
    $update_phone = mysqli_real_escape_string($conn, $_POST['update_phone'] ?? '');

    $stmt = $conn->prepare("UPDATE `user_db` SET name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("sssi", $update_name, $update_email, $update_phone, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['user_name'] = $update_name;
        $_SESSION['user_email'] = $update_email;
        $_SESSION['user_phone'] = $update_phone;
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=Edit Profile Successful!');
        exit();
    } else {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?error=Server Down! Please Try Again.');
        exit();
    }
}

if (isset($_POST['update_password'])) {
    $old_pass = $_POST['old_pass'] ?? '';
    $new_pass = mysqli_real_escape_string($conn, $_POST['new_pass'] ?? '');
    $confirm_pass = mysqli_real_escape_string($conn, $_POST['confirm_pass'] ?? '');

    $result = mysqli_query($conn, "SELECT * FROM `user_db` WHERE id = '$user_id'");
    $user = mysqli_fetch_assoc($result);

    if (password_verify($old_pass, $user['password'] ?? '')) {
        if ($new_pass == $confirm_pass) {
            $hash_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE `user_db` SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hash_new_pass, $user_id);

            if ($stmt->execute()) {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?success=Password Updated Successful!');
                exit();
            } else {
                header('Location: ' . $_SERVER['PHP_SELF'] . '?error=Server Down! Please Try Again.');
                exit();
            }
        } else {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?error=The new passwords entered do not match. Please try again.');
            exit();
        }
    } else {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?error=Old password is incorrect! The password update was unsuccessful. Please try again with the correct password.');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update profile</title>
   <style>
       input[name="user_phone"].readonly,
       input[name="update_phone"].readonly {
            background-color: #fff;
            color: #000;
            border: 1px solid #ccc;
        }
   </style>
   <link rel="stylesheet" href="css/style2.css">
   <?php require('inc/links.php'); ?>
   <?php require('inc/header.php'); ?>
</head>
<body>
   
    <?php
       $select = mysqli_query($conn, "SELECT * FROM `user_db` WHERE id = '$user_id'") or die('query failed');
       if(mysqli_num_rows($select) > 0){
          $fetch = mysqli_fetch_assoc($select);
       }
    ?>
    
    <?php if (isset($_GET['success'])): ?>
       <div class="alert alert-success alert-dismissible fade show" role="alert">
             <?php echo $_GET['success']; ?>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
       </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_GET['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>


    <div class="update-profile">
        <form action="" method="post">
          <h3>My Profile</h3>
            <div class="flex">
                <div class="inputBox">
                    <span>Username :</span>
                    <input type="text" name="user_name" value="<?php echo $user_name; ?>" class="form-control" readonly>
                    <span>Email :</span>
                    <input type="email" name="user_email" value="<?php echo $user_email; ?>" class="form-control" readonly>
                    <span>Phone Number :</span>
                    <input type="number" name="user_phone" value="<?php echo $user_phone; ?>" class="form-control readonly" readonly>
                </div>
            </div>
            <div class="d-flex justify-content-between">
             <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
             <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editPassword">Edit Password</button>
            </div>
            <a href="index.php" class="btn btn-secondary">Close</a>
        </form>
    </div>
    
    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label>Username :</label>
                            <input type="text" name="update_name" value="<?php echo $user_name; ?>" class="form-control">
                            <label>Email :</label>
                            <input type="email" name="update_email" value="<?php echo $user_email; ?>" class="form-control">
                            <label>Phone Number :</label>
                            <input type="number" name="update_phone" value="<?php echo $user_phone; ?>" class="form-control readonly">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="update_profile">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Password Modal -->
    <div class="modal fade" id="editPassword" tabindex="-1" aria-labelledby="editPasswordLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPasswordLabel">Edit Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                   <form action="" method="post">
                      <div class="form-group">
                            <label>Old Password :</label>
                            <input type="password" name="old_pass" class="form-control">
                            <label>New Password :</label>
                            <input type="password" name="new_pass" class="form-control">
                            <label>Confirm New Password :</label>
                            <input type="password" name="confirm_pass" class="form-control">
                      </div>
                      <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="update_password">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>
                   </form>
                </div>
            </div>
        </div>
    </div>



    <?php require('inc/footer.php') ?>
</body>
</html>
