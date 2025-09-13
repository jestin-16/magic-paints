<?php
$servername = "localhost";
$username = "root";
$password = "";
$con = mysqli_connect($servername, $username, $password);
if (!$con) {
    die("Connection error");
}
$dbname = "projectm";
mysqli_select_db($con, $dbname);

// Handle AJAX Update Request
if (isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $building_type = mysqli_real_escape_string($con, $_POST['building_type']);
    $work_type = mysqli_real_escape_string($con, $_POST['work_type']);
    $square_feet = mysqli_real_escape_string($con, $_POST['square_feet']);
    $status = mysqli_real_escape_string($con, $_POST['status']);

    $update_query = "UPDATE bookingdetails SET 
        Name = '$name',
        phoneNo = '$phone',
        address = '$address',
        build_type = '$building_type',
        work_type = '$work_type',
        sqfeet = '$square_feet',
        status = '$status'
        WHERE id = '$id'";

    if (mysqli_query($con, $update_query)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($con); // Add error message for debugging
    }
    exit();
}

// Handle AJAX Delete Request
if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = $_POST['id'];
    $delete_query = "DELETE FROM `bookingdetails` WHERE id='$id'";
    if (mysqli_query($con, $delete_query)) {
        echo "success";
    } else {
        echo "error";
    }
    exit();
}

// Handle AJAX View Request
if (isset($_POST['action']) && $_POST['action'] == 'view') {
    $id = $_POST['id'];
    $query = "SELECT * FROM `bookingdetails` WHERE id='$id'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Display the details in a formatted way
        echo "<p><strong>Work ID:</strong> " . $row['id'] . "</p>";
        echo "<p><strong>Username:</strong> " . $row['username'] . "</p>";
        echo "<p><strong>Name:</strong> " . $row['Name'] . "</p>";
        echo "<p><strong>Email:</strong> " . $row['email'] . "</p>";
        echo "<p><strong>Phone:</strong> " . $row['phoneNo'] . "</p>";
        echo "<p><strong>Address:</strong> " . $row['address'] . "</p>";
        echo "<p><strong>Location:</strong> " . $row['location'] . "</p>";
        echo "<p><strong>Building Type:</strong> " . $row['build_type'] . "</p>";
        echo "<p><strong>Work Type:</strong> " . $row['work_type'] . "</p>";
        echo "<p><strong>Square Feet:</strong> " . $row['sqfeet'] . "</p>";
        echo "<p><strong>Status:</strong> " . $row['status'] . "</p>";
        echo "<p><strong>Time:</strong> " . $row['created_at'] . "</p>";
    } else {
        echo "<p>No details found for this Work Order.</p>";
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Orders Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f8fa;
        }

        .container-title {
            text-align: center;
            padding: 20px;
            background-color: #343a40;
            color: white;
        }

        .table-container {
            max-width: 1200px;
            margin: 20px auto;
        }

        .table {
            background-color: white;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .modal-header {
            background-color: #007bff;
            color: white;
        }

        .search-box {
            margin-bottom: 15px;
        }

        .action-btns {
    display: flex; /* Use flexbox to align buttons horizontally */
    gap: 10px; /* Add some space between the buttons */
}

.action-btns .btn {
    white-space: nowrap; /* Ensure buttons don't wrap */
    padding: 5px 10px; /* Adjust button padding for better spacing */
}
.search-box {
    margin-bottom: 0; /* Remove the existing bottom margin */
}

.d-flex .btn {
    flex-shrink: 0; /* Prevent the button from shrinking */
}

.d-flex .form-control {
    flex-grow: 1; /* Allow the search box to grow and fill available space */
}
    </style>
</head>
<body>
    <div class="container-title">
        <h3>Work Orders Report</h3>
    </div>
    <div class="container">
    <div class="d-flex align-items-center mb-3">
        <a href="http://localhost/project/admin/home.php" class="btn btn-primary mr-3">Home</a>
        <input type="text" class="form-control search-box" id="searchInput" placeholder="Search Work Orders...">
    </div>
</div>
    <div class="container table-container">
        
        <?php
        $sql = "SELECT id, username, Name, phoneNo, address, build_type, work_type, sqfeet, status FROM `bookingdetails` WHERE `status`='Ongoing'";
        $res = mysqli_query($con, $sql);

        if (mysqli_num_rows($res) > 0) {
            echo "<div class='table-responsive'>";
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Work ID</th>";
           
            echo "<th>Name</th>";
            echo "<th>Phone Number</th>";
            echo "<th>Address</th>";
            echo "<th>Building Type</th>";
            echo "<th>Work Type</th>";
            echo "<th>Square Feet</th>";
            echo "<th>Status</th>";
            echo "<th>Actions</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody id='workOrdersTable'>";
            while ($x = mysqli_fetch_array($res)) {
                echo "<tr data-id='".$x['id']."'>";
                echo "<td class='work-id'>".$x['id']."</td>";
                
                echo "<td class='name'>".$x['Name']."</td>";
                echo "<td class='phone'>".$x['phoneNo']."</td>";
                echo "<td class='address'>".$x['address']."</td>";
                echo "<td class='building-type'>".$x['build_type']."</td>";
                echo "<td class='work-type'>".$x['work_type']."</td>";
                echo "<td class='sqfeet'>".$x['sqfeet']."</td>";
                echo "<td class='status'>".$x['status']."</td>";
                echo "<td class='action-btns'>";
                echo "<button class='btn btn-sm btn-info view-btn btn-action'>View</button>";
                echo "<button class='btn btn-sm btn-warning edit-btn btn-action'>Edit</button>";
                echo "<button class='btn btn-sm btn-danger delete-btn btn-action'>Delete</button>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            echo "</div>";
        } else {
            echo "<div class='alert alert-warning'>No records found.</div>";
        }
        ?>
    </div>

    <!-- Modal for Editing -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Work Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="workId" />
                       
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" id="name">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" class="form-control" id="phone">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" class="form-control" id="address">
                        </div>
                        <div class="form-group">
                            <label>Building Type</label>
                            <input type="text" class="form-control" id="buildingType">
                        </div>
                        <div class="form-group">
                            <label>Work Type</label>
                            <select class="form-control" id="workType">
                                <option value="Square feet">Square Feet</option>
                                <option value="Tatch">Tatch</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Square Feet</label>
                            <input type="text" class="form-control" id="squareFeet">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control" id="status">
                                <option value="Pending">Pending</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveChangesBtn">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Viewing Details -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewModalLabel">Work Order Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="viewDetails"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Search functionality
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $("#workOrdersTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // View button
            $('.view-btn').click(function () {
                var id = $(this).closest('tr').data('id');
                $.post('', { action: 'view', id: id }, function (data) {
                    $('#viewDetails').html(data);
                    $('#viewModal').modal('show');
                });
            });

            // Edit button
            $('.edit-btn').click(function () {
                var id = $(this).closest('tr').data('id');
                var row = $(this).closest('tr');
                
                // Populate the form with the existing values
                $('#workId').val(id);
                $('#username').val(row.find('.username').text());
                $('#name').val(row.find('.name').text());
                $('#phone').val(row.find('.phone').text());
                $('#address').val(row.find('.address').text());
                $('#buildingType').val(row.find('.building-type').text());
                $('#workType').val(row.find('.work-type').text());
                $('#squareFeet').val(row.find('.sqfeet').text());
                $('#status').val(row.find('.status').text());
                
                $('#editModal').modal('show');
            });

            // Save changes
            $('#saveChangesBtn').click(function () {
                var formData = {
                    action: 'update',
                    id: $('#workId').val(),
                    name: $('#name').val(),
                    phone: $('#phone').val(),
                    address: $('#address').val(),
                    building_type: $('#buildingType').val(),
                    work_type: $('#workType').val(),
                    square_feet: $('#squareFeet').val(),
                    status: $('#status').val()
                };

                $.post('', formData, function (response) {
                    if (response == 'success') {
                        alert('Work order updated successfully');
                        location.reload();
                    } else {
                        alert('Error updating work order: ' + response);
                    }
                });
            });

            // Delete button
            $('.delete-btn').click(function () {
                var id = $(this).closest('tr').data('id');
                if (confirm("Are you sure you want to delete this work order?")) {
                    $.post('', { action: 'delete', id: id }, function (response) {
                        if (response == 'success') {
                            alert('Work order deleted successfully');
                            location.reload(); // Reload page to reflect changes
                        } else {
                            alert('Error deleting work order');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
