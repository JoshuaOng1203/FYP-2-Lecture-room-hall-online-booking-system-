<?php
@include 'config.php';
session_start();

// Fetch learning spaces data from the database
$query = "SELECT `id`, `space_name`, `building`, `location`, `capacity`, `equipments`, `start_time`, `end_time`, `image` FROM `ums_learning_spaces` WHERE 1";
$result = mysqli_query($conn, $query);

$learningSpaces = array();

while ($spaceData = mysqli_fetch_assoc($result)) {
    $spaceData['equipments'] = explode(', ', $spaceData['equipments']); // Split the equipment string into an array
    $learningSpaces[] = $spaceData;
}

include('inc/header.php');
include('inc/topbar.php');
include('inc/sidebar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Learning Space Management Page</title>
   

</head>
<body>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="container-fluid">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>
                                Learning Space
                                <a data-toggle="modal" data-target="#add_learning_space" class="btn btn-primary float-right">Add</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Learning Space</th>
                                        <th>Building</th>
                                        <th>Location</th>
                                        <th>Capacity</th>
                                        <th>Equipments</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Images</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $counter = 1;
                                    foreach ($learningSpaces as $space) {
                                    ?>
                                        <tr>
                                            <td><?php echo $counter; ?></td>
                                            <td><?php echo $space['space_name']; ?></td>
                                            <td><?php echo $space['building']; ?></td>
                                            <td><?php echo $space['location']; ?></td>
                                            <td><?php echo $space['capacity']; ?></td>
                                            <td>
                                                <?php
                                                // Display each equipment from the array
                                                foreach ($space['equipments'] as $equipment) {
                                                    echo $equipment . "<br>";
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $space['start_time']; ?></td>
                                            <td><?php echo $space['end_time']; ?></td>
                                            <td>
                                                <?php                                          
                                                // Display the image with the updated source path
                                                $imagePath = 'images/learning_spaces/' . basename($space['image']);
                                                if (file_exists($imagePath) && is_readable($imagePath)) {
                                                    echo '<img src="' . $imagePath . '" alt="Learning Space Image" width="100">';
                                                } else {
                                                    echo 'Image not found';
                                                }
                                                ?>
                      </td>
                      <td>
                        <button type="button" class="btn btn-success editBtn" data-toggle="modal" data-target="#edit_learning_space" data-space-id="<?php echo $space['id']; ?>">Edit</button>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $space['id']; ?>">Delete</button>
                    </tr>
                      <!-- Delete Learning Space Confirmation -->
                      <div class="modal fade" id="deleteModal<?php echo $space['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete this space?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <a href="delete_learning_space.php?id=<?php echo $space['id']; ?>" class="btn btn-danger">Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                      $counter++;
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Add Learning Space Modal -->
<div class="modal fade" id="add_learning_space" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Learning Space</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="process_learning_space.php" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group">
            <label for="image" class="form-label fw-bold">Add Image:</label>
            <input type="file" id="image" name="image" accept=".jpg, .png, .webp, .jpeg" class="form-control">
            <input type="hidden" name="room_id">
          </div>
          <div class="form-group">
            <label for="space_name">Space Name:</label>
            <input type="text" id="space_name" name="name" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="building">Building:</label>
            <input type="text" id="building" name="building" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="capacity">Capacity:</label>
            <input type="number" id="capacity" name="capacity" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="capacity">Start Time:</label>
            <input type="time" id="start_time" name="start_time" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="capacity">End Time:</label>
            <input type="time" id="end_time" name="end_time" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="equipments">Equipments:</label>
            <div class="row">
              <div class="form-check">
                <input type="checkbox" name="equipment[]" id="flip_chair" value="Flip chair">
                <label for="flip_chair">Flip chair</label>
              </div>
              <div class="form-check">
                <input type="checkbox" name="equipment[]" id="Air_conditioner" value="Air conditioner">
                <label for="Air_conditioner">Air conditioner</label>
              </div>
              <div class="form-check">
                <input type="checkbox" name="equipment[]" id="Whiteboard" value="Whiteboard">
                <label for="Whiteboard">Whiteboard</label>
              </div>
              <div class="form-check">
                <input type="checkbox" name="equipment[]" id="Screen_layer" value="Screen layer">
                <label for="Screen_layer">Screen layer</label>
              </div>
              <div class="form-check">
                <input type="checkbox" name="equipment[]" id="LCD_projecter" value="LCD projector">
                <label for="LCD_projecter">LCD projector</label>
              </div>
              <div class="form-check">
                <input type="checkbox" name="equipment[]" id="PC_desktop" value="PC desktop">
                <label for="PC_desktop">PC desktop</label>
              </div>
              <div class="form-check">
                <input type="checkbox" name="equipment[]" id="Banquet_chair" value="Banquet chair">
                <label for="Banquet_chair">Banquet chair</label>
              </div>
              <div class="form-check">
                <input type="checkbox" name="equipment[]" id="Training_table" value="Training table">
                <label for="Training_table">Training table</label>
              </div>
              <div class="form-check">
                <input type="checkbox" name="equipment[]" id="Training_chair" value="Training chair">
                <label for="Training_chair">Training chair</label>
              </div>
              <div class="form-check">
                <input type="checkbox" name="equipment[]" id="Folding_chair" value="Folding chair">
                <label for="Folding_chair">Folding chair</label>
              </div>
              <div class="form-check">
                <input type="checkbox" name="equipment[]" id="Microphone" value="Microphone">
                <label for="Microphone">Microphone</label>
              </div>
              <div class="form-check">
                <input type="checkbox" name="equipment[]" id="Smartboard" value="Smartboard">
                <label for="Smartboard">Smartboard</label>
              </div>
              <div class="form-check">
                <input type="checkbox" name="equipment[]" id="Desktop_computer" value="Desktop computer">
                <label for="Desktop_computer">Desktop computer</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" name="add_space" class="btn btn-primary">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Learning Space Modal -->
<div class="modal fade" id="edit_learning_space" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Learning Space</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="process_learning_space.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                  <!-- Hidden input to store the image file name -->
                  <input type="hidden" id="edit_room_id" name="room_id">
                  <input type="hidden" id="edit_original_image_filename" name="edit_original_image_filename">

                  <!-- Text input to display the image file path -->
                  <div class="form-group">
                      <label for="edit_image" class="form-label fw-bold">Edit Image:</label>
                      <div class="input-group">
                          <input type="text" id="edit_image" name="image" class="form-control" readonly>
                          <div class="input-group-append">
                              <button type="button" class="btn btn-secondary" id="changeImage">Change</button>
                          </div>
                      </div>
                      
                  </div>

                  <!-- Existing code for image upload -->
                  <div class="form-group">
                      <input type="file" id="edit_image_input" name="image" accept=".jpg, .png, .webp, .jpeg" style="display: none;">
                  </div>

                    <!-- Existing code for space name -->
                    <div class="form-group">
                        <label for="edit_space_name">Space Name:</label>
                        <input type="text" id="edit_space_name" name="name" class="form-control" required>
                    </div>
                    <!-- Existing code for building -->
                    <div class="form-group">
                        <label for="edit_building">Building:</label>
                        <input type="text" id="edit_building" name="building" class="form-control" required>
                    </div>
                    <!-- Existing code for location -->
                    <div class="form-group">
                        <label for="edit_location">Location:</label>
                        <input type="text" id="edit_location" name="location" class="form-control" required>
                    </div>
                    <!-- Existing code for capacity -->
                    <div class="form-group">
                        <label for="edit_capacity">Capacity:</label>
                        <input type="number" id="edit_capacity" name="capacity" class="form-control" required>
                    </div>
                    <!-- Existing code for start time -->
                    <div class="form-group">
                        <label for="edit_start_time">Start Time:</label>
                        <input type="time" id="edit_start_time" name="edit_start_time" class="form-control" required>
                    </div>
                    <!-- Existing code for end time -->
                    <div class="form-group">
                        <label for="edit_end_time">End Time:</label>
                        <input type="time" id="edit_end_time" name="edit_end_time" class="form-control" required>
                    </div>
                    <!-- Existing code for equipments -->
                    <div class="form-group">
                        <label for="edit_equipments">Equipments:</label>
                        <div class="row">
                            <div class="form-check">
                                <input type="checkbox" name="edit_equipment[]" id="edit_flip_chair" value="Flip chair">
                                <label for="edit_flip_chair">Flip chair</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="edit_equipment[]" id="edit_Air_conditioner" value="Air conditioner">
                                <label for="edit_Air_conditioner">Air conditioner</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="edit_equipment[]" id="edit_Whiteboard" value="Whiteboard">
                                <label for="edit_Whiteboard">Whiteboard</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="edit_equipment[]" id="edit_Screen_layer" value="Screen layer">
                                <label for="edit_Screen_layer">Screen layer</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="edit_equipment[]" id="edit_LCD_projector" value="LCD projector">
                                <label for="edit_LCD_projector">LCD projector</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="edit_equipment[]" id="edit_PC_desktop" value="PC desktop">
                                <label for="edit_PC_desktop">PC desktop</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="edit_equipment[]" id="edit_Training_table" value="Training table">
                                <label for="edit_Training_table">Training table</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="edit_equipment[]" id="edit_Training_chair" value="Training chair">
                                <label for="edit_Training_chair">Training chair</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="edit_equipment[]" id="edit_Folding_chair" value="Folding chair">
                                <label for="edit_Folding_chair">Folding chair</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="edit_equipment[]" id="edit_Microphone" value="Microphone">
                                <label for="edit_Microphone">Microphone</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="edit_equipment[]" id="edit_Smartboard" value="Smartboard">
                                <label for="edit_Smartboard">Smartboard</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="edit_equipment[]" id="edit_Desktop_computer" value="Desktop computer">
                                <label for="edit_Desktop_computer">Desktop computer</label>
                            </div>
                            </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="edit_space" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    $(document).ready(function () {
        $('.editBtn').click(function () {
            var spaceId = $(this).data('space-id');
            $('#edit_room_id').val(spaceId);

            // Ajax request to get space data
            $.ajax({
                type: "GET",
                url: 'process_learning_space.php',
                data: { id: spaceId, action: 'get_space_data' },
                dataType: "json",
                success: function (data) {
                    // Prefill form fields with retrieved data
                    $('#edit_space_name').val(data.space_name);
                    $('#edit_building').val(data.building);
                    $('#edit_location').val(data.location);
                    $('#edit_capacity').val(data.capacity);
                    $('#edit_start_time').val(data.start_time);
                    $('#edit_end_time').val(data.end_time);

                    // Uncheck all checkboxes
                    $('#edit_equipments input[type="checkbox"]').prop('checked', false);

                    // Check checkboxes for each equipment
                    data.equipments.forEach(function (equipment) {
                        var trimmedEquipment = equipment.trim();
                        var checkbox = $('#edit_learning_space input[name="edit_equipment[]"][value="' + trimmedEquipment + '"]');
                        if (checkbox.length) {
                            checkbox.prop('checked', true);
                        }
                    });

                    // Output the image path for debugging
                $('#imagePathDebug').text('Image Path: ' + data.image);

                    // Show the edit modal
                    $('#edit_learning_space').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Add the click event for the "Change" button
        $('#changeImage').click(function () {
            $('#edit_image_input').click(); // Trigger click on the file input
        });

        // Add the change event for the file input
        $('#edit_image_input').change(function () {
            var fileName = $(this).val().split('\\').pop(); // Get the file name
            $('#edit_image').val(fileName); // Set the file name in the text input
        });

        // Add the hidden.bs.modal event to clear the image source when the modal is closed
        $('#edit_learning_space').on('hidden.bs.modal', function () {
            // Reset the image source and hide the modal
            $('#edit_image_preview').attr('src', '');
            $('#edit_learning_space').modal('hide');
        });
    });
</script>
</body>
</html>

<?php include('inc/scripts.php'); ?>


