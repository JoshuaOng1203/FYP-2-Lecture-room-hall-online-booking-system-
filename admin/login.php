<?php

@include 'config.php';

session_start();

if(isset($_POST['submit'])){
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, $_POST['password']);

   $select = " SELECT * FROM admin_db WHERE email = '$email' && password = '$pass' ";
   
   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){
      $row = mysqli_fetch_array($result);
      var_dump($row);
      if($row['user_type'] == 'admin'){
         $_SESSION['admin_name'] = $row['name'];
         $_SESSION['admin_id'] = $row['id'];
         header('location:index.php');
      }
   }else{
      $error[] = 'Incorrect email or password!';
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
      <h3>Admin Login</h3>
      <?php
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         }
      }
      ?>
      <input type="email" name="email" required placeholder="Enter your email">
      <input type="password" name="password" required placeholder="Enter your password">
      <input type="submit" name="submit" value="Login Now" class="form-btn">
      <p> <a href="../user/login.php" style="display: inline-block; padding: 10px 30px; font-size: 20px; cursor: pointer; width: 440px; background: #2ec1ac; color: #fff; border-radius: 5px; margin-right: 10px; transition: background 0.3s, transform 0.3s;" onmouseover="this.style.transform='scale(1.05)';" onmouseout="this.style.transform='scale(1)';">User Login Page</a></p>
   </form>

</div>

</body>
</html>
