<?php
include('config.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

    $select = "SELECT * FROM user_db WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if (!$result) {
        die('Error: ' . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_array($result);

        if (password_verify($pass, $row['password']) && $row['user_type'] == 'user') {
            if ($row['status'] == 1) {
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_phone'] = $row['phone'];
                $_SESSION['user_id'] = $row['id'];
                header('location:index.php');
                exit(); // Ensure no further execution
            } else {
                $error[] = 'Your account is not yet verified. Please check your email for the verification code.';
            }
        } else {
            $error[] = 'Incorrect email or password!';
        }
    } else {
        $error[] = 'User not found!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="form-container">
        <form action="" method="post">
            <h3>User Login</h3>
            <?php
            if (isset($error)) {
                foreach ($error as $errorMsg) {
                    echo '<span class="error-msg">' . htmlspecialchars($errorMsg) . '</span>';
                }
            }
            ?>
            <input type="email" name="email" required placeholder="Enter your email">
            <input type="password" name="password" required placeholder="Enter your password">
            <input type="submit" name="submit" value="Login Now" class="form-btn">
            <a href="../admin/login.php" style="display: inline-block; padding: 10px 30px; font-size: 20px; cursor: pointer; width: 440px; background: #2ec1ac; color: #fff; border-radius: 5px; margin-right: 10px; transition: background 0.3s, transform 0.3s;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">Admin Login Page</a>
            <p>Don't have an account? <a href="register.php">Register Now</a></p>
            <p>Forget Password? <a href="recover_password.php">Click Here</a></p>
        </form>
    </div>
</body>

</html>
