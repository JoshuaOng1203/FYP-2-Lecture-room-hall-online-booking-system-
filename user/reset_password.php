<?php 
@include 'config.php';

session_start();
?>

<?php
if (isset($_POST["reset"])) {
    $pass = $_POST["password"];

    // Assuming your connection variable is $conn, modify if it's named differently
    if (!$conn) {
        die('Error: ' . mysqli_connect_error());
    }

    $hash = password_hash($pass, PASSWORD_DEFAULT);

    $token = $_SESSION['token'];
    $Email = $_SESSION['email'];

    $sql = mysqli_query($conn, "SELECT * FROM user_db WHERE email='$Email'");
    $fetch = mysqli_fetch_assoc($sql);

    if ($fetch) {
        $new_pass = $hash;
        mysqli_query($conn, "UPDATE user_db SET password='$new_pass' WHERE email='$Email'");
        ?>
        <script>
            window.location.replace("login.php");
            alert("<?php echo "Your password has been successfully reset"?>");
        </script>
        <?php
    } else {
        ?>
        <script>
            alert("<?php echo "Please try again"?>");
        </script>
        <?php
    }
}
?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Reset Password</title>
      <link rel="stylesheet" href="css/style.css">
   </head>
   <body>
      <div class="form-container">
         <form action="" method="post">
            <h3>Password Reset Form</h3>

            <input type="password" name="password" required placeholder="Enter New Password Here">
            <input type="submit" name="reset" value="Reset Password" class="form-btn">

         </form>
      </div>
   </body>
</html>


