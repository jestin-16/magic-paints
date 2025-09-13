<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "projectm"; 

// Create connection
$con = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch workers
$sql = "SELECT * FROM `customer` WHERE `role`='worker'";
$res = mysqli_query($con, $sql);

// Fetch ongoing work details
$bsql = "SELECT `id`, `Name` FROM `bookingdetails` WHERE `status` LIKE '%ongoing%'";
$bres = mysqli_query($con, $bsql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table thead th {
            background-color: #0eccf3;
            color: white;
            vertical-align: middle;
        }
        .table td, .table th {
            text-align: center;
        }
        input[type="date"] {
            width: 100%;
        }
        input.attmark {
            width: 30px;
            height: 30px;
            -webkit-appearance: none; 
            -moz-appearance: none;
            appearance: none;
            border: 2px solid #0eccf3; 
            border-radius: 5px; 
            cursor: pointer;
        }
        input.attmark:checked {
            background-color: #0eccf3; 
            border-color: #0eccf3;
        }
    </style>
    <script>
        // Prevent back navigation and redirect
        window.history.pushState({page: 1}, "", ""); // Push a new state
        window.onpopstate = function () {
            window.location.href = "http://localhost/project/admin/home.php"; // Redirect to home page
        };
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2>Attendance</h2>
    
        <form method="POST" action="#">
            <div class="mb-3">
                <label for="mainDatePicker" class="form-label">Select Date:</label>
                <input type="date" id="mainDatePicker" class="form-control" name="attdate">
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Work type</th>
                        <th>Site</th>
                        <th>Session 1</th>
                        <th>Session 2</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($x = mysqli_fetch_assoc($res)) {
                        echo "<tr>
                                <td>{$x['cust_name']}<input type='hidden' name='cust_id[]' value='{$x['cust_id']}'></td>
                                <td>
                                    <select name='Worktype[{$x['cust_id']}]' class='form-select'>
                                        <option>painting</option>
                                        <option>design</option>
                                        <option>Texture</option>
                                    </select>
                                </td>
                                <td>
                                    <select name='Site[{$x['cust_id']}]' class='form-select'>";
                        // Loop through ongoing works to populate the Site dropdown
                        mysqli_data_seek($bres, 0); // Reset result pointer
                        while ($rs = mysqli_fetch_assoc($bres)) {
                            echo "<option value='{$rs['id']}'>{$rs['Name']}</option>";
                        }
                        echo "      </select>
                                </td>
                                <td><input type='checkbox' class='attmark' name='session1[{$x['cust_id']}]' value='1' checked></td>
                                <td><input type='checkbox' class='attmark' name='session2[{$x['cust_id']}]' value='1' checked></td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
            <center>
                <a href="http://localhost/project/admin/home.php" class="btn btn-primary">Home</a>
                <input type="submit" value="Update" name="update" class="btn btn-primary">
            </center>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
if (isset($_POST['update'])) {
    $date = $_POST['attdate'];
    $employeeIds = $_POST['cust_id'];
    $worktypes = $_POST['Worktype'];
    $sites = $_POST['Site'];  // Capture the site field values
    $success = true; // Flag to check if any error occurs

    foreach ($employeeIds as $id) {
        $session1 = isset($_POST['session1'][$id]) ? 1 : 0;
        $session2 = isset($_POST['session2'][$id]) ? 1 : 0;
        $worktype = $worktypes[$id];
        $site = $sites[$id]; // Get the corresponding site value

        // Insert into the attendance table
        $sql = "INSERT INTO `attendance`(`Emp_Id`, `_date`, `Worktype`, `_site`, `Session1`, `Session2`) VALUES 
        ('$id', '$date', '$worktype', '$site', '$session1', '$session2')";
        $res = mysqli_query($con, $sql);

        if (!$res) {
            echo "Error updating record for ID $id: " . mysqli_error($con);
            $success = false; // Set flag to false if any error occurs
        }
    }

    if ($success) {
        echo "<script>alert('Updated successfully. Redirecting to home page.'); window.location.href = 'http://localhost/project/admin/home.php';</script>";
    }
}
?>
