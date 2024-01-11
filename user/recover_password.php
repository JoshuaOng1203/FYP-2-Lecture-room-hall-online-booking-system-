<?php 
@include 'config.php';

session_start();
?>

<?php 
    if(isset($_POST["recover"])){
        include('config.php');
        $email = $_POST["email"];

        $sql = mysqli_query($conn, "SELECT * FROM user_db WHERE email='$email'");
        $query = mysqli_num_rows($sql);
  	    $fetch = mysqli_fetch_assoc($sql);

        if(mysqli_num_rows($sql) <= 0){
            ?>
            <script>
                alert("<?php  echo "Sorry, no emails exists "?>");
            </script>
            <?php
        }else if($fetch["status"] == 0){
            ?>
               <script>
                   alert("Sorry, your account must verify first, before you recover your password !");
                   window.location.replace("login.php");
               </script>
           <?php
        }else{
            // generate token by binaryhexa 
            $token = bin2hex(random_bytes(50));

            //session_start ();
            $_SESSION['token'] = $token;
            $_SESSION['email'] = $email;

            require "Mail/phpmailer/PHPMailerAutoload.php";
            $mail = new PHPMailer;

            $mail->isSMTP();
            $mail->Host='smtp.gmail.com';
            $mail->Port=587;
            $mail->SMTPAuth=true;
            $mail->SMTPSecure='tls';

            // h-hotel account
            $mail->Username='friendlyming338@gmail.com';
            $mail->Password='moxp bpha rmqq phia';

            // send by h-hotel email
            $mail->setFrom('friendlyming338@gmail.com', 'Password Reset');
            // get email from input
            $mail->addAddress($_POST["email"]);
            

            // HTML body
            $mail->isHTML(true);
            $mail->Subject="Recover your password";
            $mail->Body="<b>Dear User</b>
            <h3>We received a request to reset your password.</h3>
            <p>Kindly click the below link to reset your password</p>
            https://lecroom.xyz/user/reset_password.php
            <br><br>";

            if(!$mail->send()){
                ?>
                    <script>
                        alert("<?php echo " Invalid Email "?>");
                    </script>
                <?php
            }else{
                ?>
                    <script>
                        alert("<?php echo " Password recover link already sent out, please check your email. "?>");
                        window.location.replace("login.php");
                    </script>
                <?php
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
      <title>Recover Password</title>
      <link rel="stylesheet" href="css/style.css">
   </head>
   <body>
      <div class="form-container">
         <form action="" method="post">
            <h3>Password Recover</h3>

            <input type="email" name="email" required placeholder="Enter your email">
            <input type="submit" name="recover" value="Send Password Recover Link" class="form-btn">

         </form>
      </div>
   </body>
</html>


