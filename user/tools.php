<?php
include ('config.php');
session_start();

// Fetch tool data from the database
$query = "SELECT `id`, `tool_name`, `image` FROM `tools` WHERE 1";
$result = mysqli_query($conn, $query);

$tools = array();

while ($toolData = mysqli_fetch_assoc($result)) {
    $tools[] = $toolData;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecture Room/Hall Online Booking System</title>
    <?php require('inc/links.php'); ?>
    
    <style>
        .pop:hover {
            border-top-color: var(--teal) !important;
            transform: scale(1.03);
            transition: all 0.3s;
        }
    </style>
</head>
<body>

    <?php require('inc/header.php'); ?>

    <div class="my-5 px-4">
        <h2 class="fw-bold text-center">Tools And Accessories</h2>
        <div class="h-line bg-dark"></div>
    </div>

    <div class="container">
        <div class="row">
            <?php foreach ($tools as $tool) : ?>
            <div class="col-lg-4 col-md-6 mb-5 px-4">
                <div class="card border-0 shadow bg-white rounded p-4 border-top border-4 border-dark pop" style="max-width: 350px; margin: auto;">
                    <?php
                    $imagePath = "../admin/{$tool['image']}";
                    if (file_exists($imagePath)) {
                        echo "<img src='{$imagePath}' class='card-img-top' alt='...' style='width: 300px; height: 200px;'>";
                    } else {
                        echo "<p>Error: Image not found - {$imagePath}</p>";
                    }
                    ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $tool['tool_name']; ?></h5>
                        <div class="d-flex justify-content-evenly mb-2">
                            <a href="tools_calendar.php?toolId=<?php echo $tool['id']; ?>" class="btn text-white custom-bg shadow-none" style="margin-top: 10px;">Check Availability</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php require('inc/footer.php'); ?>
</body>
</html>
