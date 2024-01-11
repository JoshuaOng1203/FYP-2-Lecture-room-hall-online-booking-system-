<?php

include ('config.php');
session_start();

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpassword']);
    $user_type = "user";  

    // Check if user already exists
    $select = "SELECT * FROM user_db WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $error[] = 'User already exists!';
    } elseif ($pass != $cpass) {
        $error[] = 'Password not matched!';
    } else {
        // Hash the password before storing it
        $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

        // Create the SQL query
        $insert = "INSERT INTO user_db(name, phone, email, password, user_type) VALUES('$name','$phone', '$email', '$hashedPassword', '$user_type')";
        mysqli_query($conn, $insert);
        
        // Generate and store OTP
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['mail'] = $email;

        // Send OTP via email
        require "Mail/phpmailer/PHPMailerAutoload.php";
        $mail = new PHPMailer;

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';

        $mail->Username = 'friendlyming338@gmail.com';
        $mail->Password = 'moxp bpha rmqq phia';

        $mail->setFrom('friendlyming338@gmail.com', 'OTP Verification');
        $mail->addAddress($_POST["email"]);

        $mail->isHTML(true);
        $mail->Subject = "Your verification code";
        $mail->Body = "<p>Dear user, </p> <h3>Your verification code is $otp <br></h3>
        <p>If you failed to access the verification process, please click this link to verify your account.</p>
         https://lecroom.xyz/user/verification.php
         <br></br>";

        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            $_SESSION['email_sent'] = true;
        }
        header('location:verification.php');
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register Form</title>
   <link rel="stylesheet" href="css/style.css">
   <style>
       input[type="number"]::-webkit-inner-spin-button,
       input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
   </style>
</head>
<body>
   <div class="form-container">
      <form action="" method="post">
         <h3>Register Now</h3>
         <?php
         if (isset($error)) {
            foreach ($error as $error) {
               echo '<span class="error-msg">' . $error . '</span>';
            }
         }
         ?>
         <input type="text" name="name" required placeholder="Enter your name">
         <input type="number" name="phone" required placeholder="Enter your phone number">
         <input type="email" name="email" required placeholder="Enter your email address">
         <input type="password" name="password" required placeholder="Enter your password">
         <input type="password" name="cpassword" required placeholder="Confirm your password">
         <input type="submit" name="submit" value="Register Now" class="form-btn">
         <p>Already have an account? <a href="login.php">Login Now</a></p>
      </form>
   </div>
</body>
</html>
