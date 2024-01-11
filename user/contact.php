<?php
@include 'config.php';
session_start();

// Define variables to store messages and user information
$error_message = '';
$user_info = ['name' => '', 'email' => '']; // Replace with your actual user information retrieval logic

// Check if the user is logged in and has user information
if (isset($_SESSION['user_name'])) {
    $user_info = [
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email']
    ];
}

// Initialize form fields
$name = $_POST['name'] ?? $user_info['name'];
$email = $_POST['email'] ?? $user_info['email'];
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

// If the form is submitted, use the posted values or user information
if (isset($_POST['send'])) {
    global $conn; // Assuming $conn is your database connection

    // Use posted values or user information to pre-fill the form
    $name = $_POST['name'] ?? $user_info['name'];
    $email = $_POST['email'] ?? $user_info['email'];
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Building and executing the SQL query
    $q = "INSERT INTO `user_queries` (`name`, `email`, `subject`, `message`) VALUES ('$name', '$email', '$subject', '$message')";
    $res = mysqli_query($conn, $q);

    // Update messages based on the query result
    if ($res) {
        $_SESSION['success_message'] = 'Mail Sent! Thank You for Your Feedback.';
    } else {
        $error_message = 'Server Down! Please Try Again.';
    }
    header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to clear POST data
    exit();
}

// Check and display success message from session
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LecRoom - Contact Us</title>
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <?php require('inc/links.php'); ?>
    <?php require('inc/header.php'); ?>
</head>
<body>

<div class="my-5 px-4">
    <h2 class="fw-bold text-center">CONTACT US</h2>
    <div class="h-line bg-dark"></div>
    <p class="text-center mt-3">
        If you have any enquiries, please feel free to leave a message for us!
    </p>
</div>

<div class="container">
    <div class="row">

        <div class="col-lg-6 col-md-6 mb-5 px-4">
            <div class="bg-white rounded shadow p-4 d-flex align-items-center">
                <div class="col-md-5 mb-lg-0 mb-md-0 mb-3">
                    <img src="images/contact/Madam_Ezy.jpg" class="img-fluid rounded">
                </div>
                <div class="col-md-7 px-3">
                    <h4><strong>EZY @ NOROL ATIKAH SABTU</strong></h4>
                    <br>
                    <h5 class="mt-6">Contact Me:</h5>
                    <a href="tel: +6087 - 503000" class="d-inline-block mb-2 text-decoration-none text-dark"><i
                                class="bi bi-telephone-fill"></i> +6087 - 503000</a>
                    <br>
                    <a href="tel: +6087 - 503000"
                       class="d-inline-block mb-2 text-decoration-none text-dark"><i class="bi bi-whatsapp"></i> +6087
                        - 503000</a>
                    <h5 class="mt-4">Email:</h5>
                    <a href="mailto: ezy@ums.edu.my" class="d-inline-block mb-2 text-decoration-none text-dark"><i
                                class="bi bi-envelope-fill"></i> ezy@ums.edu.my</a>
                </div>
            </div>
        </div>

        <!--Message Console-->
        <div class="col-lg-6 col-md-6 mb-5 px-4">
            <div class="bg-white rounded shadow p-4">
                <form method="post">
                    <h5>Send a message</h5>

                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Name</label>
                        <input name="name" type="text" class="form-control shadow-none" value="<?php echo htmlspecialchars($name); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Email</label>
                        <input name="email" type="email" class="form-control shadow-none" value="<?php echo htmlspecialchars($email); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Subject</label>
                        <input required name="subject" type="text" class="form-control shadow-none">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight: 500;">Message</label>
                        <textarea required name="message" class="form-control shadow-none" rows="5"
                                  style="resize: none;"></textarea>
                    </div>
                    <button type="submit" name="send" class="btn text-white custom-bg mt-3">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require('inc/footer.php'); ?>
</body>
</html>
