<?php

include ('config.php');
session_start();

if (isset($_SESSION['email_sent']) && $_SESSION['email_sent']) {
    echo "<script>alert('Your OTP number has been sent to " . $_SESSION['mail'] . ". Please check your email!');</script>";
    unset($_SESSION['email_sent']); // Clear the session variable
}

if (isset($_POST["verify"])) {
    $otp = $_SESSION['otp'];
    $email = $_SESSION['mail'];
    $otp_code = $_POST['otp_code'];

    if (!$conn) {
        die('Error: ' . mysqli_connect_error());
    }

    if ($otp != $otp_code) {
        echo "<script>alert('Invalid OTP code');</script>";
    } else {
        $updateQuery = "UPDATE user_db SET status = 1 WHERE email = '$email'";
        if (mysqli_query($conn, $updateQuery)) {
            echo "<script>alert('Verify account done, you may sign in now'); window.location.href = 'login.php';</script>";
            exit(); // Stop further execution of PHP code after redirect
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Form</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="form-container">
        <form action="" method="post">
            <h3>Verification Account</h3>
            <input type="text" name="otp_code" required placeholder="Enter The OTP Code Here">
            <input type="submit" name="verify" value="Verify Account" class="form-btn">
        </form>
    </div>
</body>

</html>
