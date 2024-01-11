<?php
include 'config.php';
session_start();

if (isset($_GET['building'])) {
    $selectedBuilding = urldecode($_GET['building']);

    // Fetch capacity options based on the selected building
    $queryCapacity = "SELECT DISTINCT capacity FROM ums_learning_spaces WHERE building = '$selectedBuilding'";
    $resultCapacity = mysqli_query($conn, $queryCapacity);

    $options = [];

    while ($rowCapacity = mysqli_fetch_assoc($resultCapacity)) {
        $options[] = $rowCapacity['capacity'];
    }

    // Fetch learning spaces based on selected filters
    $querySpaces = "SELECT * FROM ums_learning_spaces WHERE building = '$selectedBuilding'";

    // Add capacity filter if selected
    if (isset($_GET['capacity'])) {
        $selectedCapacity = urldecode($_GET['capacity']);
        $querySpaces .= " AND capacity = '$selectedCapacity'";
    }

    $resultSpaces = mysqli_query($conn, $querySpaces);

    $learningSpaces = '';

    while ($rowSpaces = mysqli_fetch_assoc($resultSpaces)) {
        // Display learning space details
        $spaceId = $rowSpaces['id']; // Get the spaceId
        $learningSpaces .= "<div class='card mb-4 border-0 shadow'>
            <div class='row g-0 p-3 align-items-center'>
                <div class='col-md-5 mb-lg-0 mb-md-0 mb-3'>
                    <img src='../admin/images/learning_spaces/" . basename($rowSpaces['image']) . "' class='img-fluid rounded'>
                </div>
                <div class='col-md-5 px-lg-3 px-md-3 px-0'>
                    <h5 class='mb-3'>" . $rowSpaces['space_name'] . "</h5>
                    <h6 class='mb-4'><i class='bi bi-geo-alt'></i>" . $rowSpaces['building'] . ' , ' . $rowSpaces['location'] . " </h6>
                    <div class='capacity mb-4'>
                        <h6 class='mb-1'>Capacity</h6>
                        <span class='badge rounded-pill bg-light text-dark text-wrap'>
                            " . $rowSpaces['capacity'] . "
                        </span>
                    </div>
                    <div class='Facilities mb-4'>
                        <h6 class='mb-1'>Facilities</h6>";

        // Display each equipment
        foreach (explode(', ', $rowSpaces['equipments']) as $equipment) {
            $learningSpaces .= "<span class='badge rounded-pill bg-light text-dark text-wrap'>
                    $equipment
                </span>";
        }

        $learningSpaces .= "</div>  
                </div>
                <div class='col-md-2 mt-lg-0 mt-md-0 mt-4 text-center'>";

        // Check if spaceId is not empty before rendering the button
        if (!empty($spaceId)) {
            // Include spaceId in the URL when redirecting to calendar.php
            $learningSpaces .= "<a href='calendar.php?spaceId={$spaceId}' class='btn w-100 text-white custom-bg shadow-none mb-2'>Check Availability</a>";
        } else {
            $learningSpaces .= "<a href='#' class='btn w-100 text-white custom-bg shadow-none mb-2' disabled>Check Availability</a>";
        }

        $learningSpaces .= "</div>
            </div>
        </div>";
    }

    // Return data as JSON including options
    echo json_encode(['options' => $options, 'learningSpaces' => $learningSpaces]);
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LecRoom - Learning Spaces</title>
    <?php require('inc/links.php'); ?>
    <link rel="stylesheet" type="text/css" href="css/common.css">
</head>
<body>

    <?php require('inc/header.php'); ?>

    <div class="my-5 px-4">
        <h2 class="fw-bold text-center">Learning Spaces</h2>
        <div class="h-line bg-dark"></div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-12 mb-lg-0 mb-4 px-0">
                <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow">
                    <div class="container-fluid flex-lg-column align-items-stretch">
                        <h4 class="mt-2">FILTERS</h4>
                        <div class="border bg-light p-3 rounded mb-3">
                            <h5 class="mb-3" style="font-size: 18px;">Building:</h5>
                            <select id="building-select" class="form-select shadow-none"
                                aria-label="Default select example">
                                <!-- Populate building options dynamically from the database -->
                                <?php
                                $buildingOptions = ["Bangunan Podium", "Bangunan Menara", "Dewan Kuliah Pusat", "Makmal Komputeran"];
                                foreach ($buildingOptions as $option) {
                                    echo "<option value='$option'>$option</option>";
                                }
                                ?>
                            </select>

                            <div style="height: 10px;"></div>

                            <div class="col-lg-12 mb-3">
                                <h5 class="mb-3" style="font-size: 18px;">Capacity:</h5>
                                <select id="capacity-select" class="form-select shadow-none"
                                    aria-label="Default select example"></select>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="col-lg-9 col-md-12 px-4" id="learning-spaces-container">
                <!-- Learning spaces will be dynamically loaded here -->
            </div>
        </div>
    </div>
    <?php require('inc/footer.php'); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buildingSelect = document.getElementById('building-select');
        const capacitySelect = document.getElementById('capacity-select');
        const learningSpacesContainer = document.getElementById('learning-spaces-container');

        function updateOptions(selectElement, options) {
            selectElement.innerHTML = '';

            if (options) {
                options.forEach((option) => {
                    const newOption = document.createElement('option');
                    newOption.text = option;
                    newOption.value = option;
                    selectElement.add(newOption);
                });
            }

            selectElement.disabled = options.length === 0;
        }

        function updateLearningSpaces() {
            const selectedBuilding = buildingSelect.value;
            const selectedCapacity = capacitySelect.value;
            const spaceId = getSpaceIdFromUrl(); // Get spaceId from URL
            const xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        const data = JSON.parse(xhr.responseText);

                        if (typeof data.learningSpaces !== 'undefined') {
                            learningSpacesContainer.innerHTML = data.learningSpaces;
                            bindCheckAvailabilityButtons();
                        } else {
                            console.error('Learning Spaces data is undefined.');
                        }
                    } else {
                        console.error('Error fetching learning spaces. Status: ' + xhr.status);
                    }
                }
            };

            // Include spaceId in the URL
            xhr.open('GET', 'spaces.php?building=' + selectedBuilding + '&capacity=' + selectedCapacity + '&spaceId=' + spaceId, true);
            xhr.send();
        }

        function updateCapacityOptions() {
            const selectedBuilding = buildingSelect.value;
            const spaceId = getSpaceIdFromUrl(); // Get spaceId from URL
            const xhr = new XMLHttpRequest();

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        const data = JSON.parse(xhr.responseText);

                        updateOptions(capacitySelect, data.options);
                        updateLearningSpaces();
                    } else {
                        console.error('Error fetching capacity options. Status: ' + xhr.status);
                    }
                }
            };

            // Include spaceId in the URL
            xhr.open('GET', 'spaces.php?building=' + selectedBuilding + '&spaceId=' + spaceId, true);
            xhr.send();
        }

        function getSpaceIdFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('spaceId') || '';
        }

        function bindCheckAvailabilityButtons() {
            const checkAvailabilityButtons = document.querySelectorAll('.check-availability-button');

            checkAvailabilityButtons.forEach(function (button) {
                button.addEventListener('click', function (event) {
                    event.preventDefault(); // Prevent the default behavior of the link
                    const spaceId = button.getAttribute('data-space-id');
                    const spaceName = button.getAttribute('data-space-name');

                    console.log("Space ID: " + spaceId + "\nSpace Name: " + spaceName);
                    alert("Space ID: " + spaceId + "\nSpace Name: " + spaceName);
                });
            });
        }

        buildingSelect.addEventListener('change', updateCapacityOptions);
        capacitySelect.addEventListener('change', updateLearningSpaces);

        updateCapacityOptions();
    });
</script>

</body>

</html>
