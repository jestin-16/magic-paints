<?php
// Database connection file
$connection = require("conn.php"); // Assuming $con is defined in conn.php

if (isset($_POST['update'])) {
    $date = $_POST['attdate'];
    $employeeIds = $_POST['cust_id'];
    $worktypes = $_POST['Worktype'];
    $workhours = $_POST['Workhours'];
    $workhours_other = $_POST['Workhours_other'];

    foreach ($employeeIds as $id) {
        $workhour = $workhours[$id];

        if ($workhour == 'other') {
            $workhour = $workhours_other[$id];
        }

        if ($workhour > 0) {
            $worktype = $worktypes[$id];
            $checkattsql = "SELECT * FROM `overtimeattendance` WHERE `Emp_Id`='$id' AND `_date`='$date'";
            $checkattres = mysqli_query($con, $checkattsql);

            if (mysqli_num_rows($checkattres) == 0) {
                $insertsql = "INSERT INTO `overtimeattendance`(`Emp_Id`, `_date`, `Worktype`, `Workhours`) VALUES ('$id', '$date', '$worktype', '$workhour')";
                mysqli_query($con, $insertsql);
            } else {
                $updatesql = "UPDATE `overtimeattendance` SET `Worktype`='$worktype', `Workhours`='$workhour' WHERE `Emp_Id`='$id' AND `_date`='$date'";
                mysqli_query($con, $updatesql);
            }
        }
    }
    // Redirect after processing all rows
    header("Location: http://localhost/project/admin/home.php");
    exit();
}

$sql = "SELECT * FROM `customer` WHERE `role`='worker'";
$res = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overtime Attendance</title>
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
</head>
<body>
    <div class="container mt-5">
        <h2>Overtime Attendance</h2>
    
        <form method="POST" action="#">
            <div class="mb-3">
                <label for="mainDatePicker" class="form-label">Select Date:</label>
                <input type="date" id="mainDatePicker" class="form-control" name="attdate">
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Work Type</th>
                        <th>Hours</th>
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
                                    <select name='Workhours[{$x['cust_id']}]' class='form-select' onchange='toggleOtherInput(this)'>
                                        <option value='0' selected>0</option>
                                        <option value='1'>1</option>
                                        <option value='2'>2</option>
                                        <option value='3'>3</option>
                                        <option value='4'>4</option>
                                        <option value='5'>5</option>
                                        <option value='6'>6</option>
                                        <option value='other'>Other</option>
                                    </select>
                                    
                                    <input type='text' name='Workhours_other[{$x['cust_id']}]' class='form-control mt-2' style='display:none;' placeholder='Enter hours' />
                                </td>
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
    <script>
    function toggleOtherInput(selectElement) {
        var otherInput = selectElement.nextElementSibling;
        if (selectElement.value === 'other') {
            otherInput.style.display = 'block';
        } else {
            otherInput.style.display = 'none';
        }
    }
    </script>
</body>
</html>
