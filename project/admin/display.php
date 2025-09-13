<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$con = mysqli_connect($servername, $username, $password);
if (!$con) {
    die("connection error");
}
$dbname = "projectm";
mysqli_select_db($con, $dbname);

// Handle form submission for editing
if (isset($_POST['update'])) {
    $cid = $_POST['id'];
    $cname = $_POST['name'];
    $cphone = $_POST['ph'];
    $crole = $_POST['role'];
    $update_sql = "UPDATE `customer` SET `cust_name`='$cname', `cust_ph`='$cphone', `role`='$crole' WHERE `cust_id`='$cid'";
    if (mysqli_query($con, $update_sql)) {
        echo "<script>
                alert('Customer updated successfully!');
                window.location.href = window.location.href;
              </script>";
    } else {
        echo "<script>alert('Error updating customer.');</script>";
    }
}

// Handle deletion (called by AJAX)
if (isset($_POST['delete'])) {
    $cid = $_POST['id'];
    $delete_sql = "DELETE FROM `customer` WHERE `cust_id`='$cid'";
    if (mysqli_query($con, $delete_sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Customer deleted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error deleting customer.']);
    }
    exit();
}

// Fetch customer records
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT `cust_id`, `cust_name`, `cust_ph`, `role` FROM `customer` WHERE `cust_name` LIKE '%$search%'";
$res = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        h2 {
            text-align: center;
            margin-top: 20px;
            color: #4B0082;
        }
        table {
            width: 100%;
            background-color: white;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #4B0082;
            color: white;
        }
        td {
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .btn {
            text-transform: uppercase;
        }
        .btn-info {
            background-color: #00bfff;
            color: white;
        }
        .btn-danger {
            background-color: #ff6347;
            color: white;
        }
        .modal-header {
            background-color: #4B0082;
            color: white;
        }
        .modal-footer .btn {
            margin-right: 10px;
        }
        .search-box {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Customer Details</h2>

    <!-- Search Box with Home Button -->
    <form action="" method="GET" class="search-box d-flex justify-content-center align-items-center">
        <a href="home.php" class="btn btn-secondary mr-2">Home</a>
        <div class="input-group" style="width: 300px;">
            <input type="text" name="search" class="form-control" placeholder="Search by name" value="<?php echo $search; ?>">
            <div class="input-group-append">
                <input type="submit" class="btn btn-primary" value="Search">
            </div>
        </div>
    </form>

    <table class="table table-bordered" id="customerTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                <tr id="row-<?php echo $row['cust_id']; ?>">
                    <td><?php echo $row['cust_id']; ?></td>
                    <td><?php echo $row['cust_name']; ?></td>
                    <td><?php echo $row['cust_ph']; ?></td>
                    <td><?php echo $row['role']; ?></td>
                    <td>
                        <button class="btn btn-info" data-toggle="modal" data-target="#editModal<?php echo $row['cust_id']; ?>">Edit</button>
                        <button class="btn btn-danger delete-btn" data-id="<?php echo $row['cust_id']; ?>">Delete</button>
                    </td>
                </tr>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $row['cust_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $row['cust_id']; ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Customer</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?php echo $row['cust_id']; ?>">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" class="form-control" value="<?php echo $row['cust_name']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="ph">Phone</label>
                                        <input type="text" name="ph" class="form-control" value="<?php echo $row['cust_ph']; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="role">Role</label>
                                        <select name="role" class="form-control">
                                            <option value="user" <?php if ($row['role'] == 'user') echo 'selected'; ?>>User</option>
                                            <option value="worker" <?php if ($row['role'] == 'worker') echo 'selected'; ?>>Worker</option>
                                            <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Update" name="update">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // Handle delete via AJAX
    $('.delete-btn').on('click', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this customer?')) {
            $.ajax({
                url: '',
                type: 'POST',
                data: { delete: true, id: id },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        $('#row-' + id).remove();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
    });
</script>
</body>
</html>
