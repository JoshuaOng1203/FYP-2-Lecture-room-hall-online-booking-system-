<?php

@include 'config.php';
session_start();

// Fetch tools data from the database
$query = "SELECT `id`, `tool_name`, `image` FROM `tools` WHERE 1";
$result = mysqli_query($conn, $query);

$tools = array();

while ($toolData = mysqli_fetch_assoc($result)) {
    $tools[] = $toolData;
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
   <title>Tool Management Page</title>
  
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
                                Tools
                                <a data-toggle="modal" data-target="#add_tool" class="btn btn-primary float-right">Add</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Tool Name</th>
                                        <th>Image</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $counter = 1;
                                    foreach ($tools as $tool) {
                                    ?>
                                        <tr>
                                            <td><?php echo $counter; ?></td>
                                            <td><?php echo $tool['tool_name']; ?></td>
                                            <td>
                                                <?php
                                                // Display the image with the updated source path
                                                $imagePath = 'images/tools/' . basename($tool['image']);
                                                if (file_exists($imagePath) && is_readable($imagePath)) {
                                                    echo '<img src="' . $imagePath . '" alt="Tool Image" width="100">';
                                                } else {
                                                    echo 'Image not found';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-success editBtn" data-toggle="modal" data-target="#edit_tool" data-tool-id="<?php echo $tool['id']; ?>">Edit</button>
                                                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteToolModal<?php echo $tool['id']; ?>">Delete</button>
                                            </td>
                                        </tr>
                                        <!-- Delete Tool Confirmation -->
                                        <div class="modal fade" id="deleteToolModal<?php echo $tool['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteToolModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteToolModalLabel">Delete Confirmation</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Are you sure you want to delete this tool?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                        <a href="delete_tool.php?id=<?php echo $tool['id']; ?>" class="btn btn-danger">Delete</a>
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

<!-- Add Tool Modal -->
<div class="modal fade" id="add_tool" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Tool</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="process_tool.php" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group">
            <label for="image" class="form-label fw-bold">Add Image:</label>
            <input type="file" id="image" name="image" accept=".jpg, .png, .webp, .jpeg" class="form-control">
            <input type="hidden" name="tool_id">
          </div>
          <div class="form-group">
            <label for="tool_name">Tool Name:</label>
            <input type="text" id="tool_name" name="name" class="form-control" required>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" name="add_tool" class="btn btn-primary">Save</button>
        </div>
       </form>
    </div>
  </div>
</div>

<!-- Edit Tool Modal -->
<div class="modal fade" id="edit_tool" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Tool</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="process_tool.php" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                  <!-- Hidden input to store the image file name -->
                  <input type="hidden" id="edit_tool_id" name="tool_id">
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

                    <!-- Existing code for tool name -->
                    <div class="form-group">
                        <label for="edit_space_name">Tool Name:</label>
                        <input type="text" id="edit_tool_name" name="name" class="form-control" required>
                    </div>                                
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="edit_tool" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    $(document).ready(function () {
        $('.editBtn').click(function () {
            var toolId = $(this).data('tool-id');
            $('#edit_tool_id').val(toolId);

            // Ajax request to get tool data
            $.ajax({
                type: "GET",
                url: 'process_tool.php',
                data: { id: toolId, action: 'get_tool_data' },
                dataType: "json",
                success: function (data) {
                    // Prefill form fields with retrieved data
                    $('#edit_tool_name').val(data.tool_name);
                                  
                    // Set the space ID and original image filename
                    $('#edit_tool_id').val(toolId);
                    $('#edit_original_image_filename').val(data.image);

                    // Show the edit modal
                    $('#edit_tool').modal('show');
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        // Add event handler for the "Change" button
        $('#changeImage').click(function () {
            // Trigger a click on the hidden file input
            $('#edit_image_input').click();
        });

        // Add an event listener for the file input to update the displayed file path
        $('#edit_image_input').change(function () {
            $('#edit_image').val($(this).val());
        });

        // Add the hidden.bs.modal event to clear the image source when the modal is closed
        $('#edit_tool').on('hidden.bs.modal', function () {
            // Reset the image source and hide the modal
            $('#edit_image_preview').attr('src', '');
            $('#edit_tool').modal('hide');
        });
    });
</script>
</body>
</html>

<?php include('inc/scripts.php'); ?>
