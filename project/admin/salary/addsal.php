<?php
$conn = mysqli_connect("localhost", "root", "");
if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}
mysqli_select_db($conn, 'projectm');

$sql = "SELECT cust_id, cust_name FROM customer WHERE role='worker'";
$res = mysqli_query($conn, $sql);
if (!$res) {
    die("Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Entry</title>
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
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Salary Management</h2>
    <div class="mb-3">
        <form method="POST" action="#">
            <label for="mainDatePicker" class="form-label">Select Date:</label>
            <input type="date" id="mainDatePicker" name="_date" class="form-control">
            <input type="submit" name="caldat" value="Calculate" class="btn btn-primary mt-3">
        </form>
    </div>

    <?php
    if (isset($_POST['caldat'])) {
        $date = $_POST['_date'];
        echo "<h4>Attendance for: " . $date . "</h4>";

        // Fetch attendance records for the selected date
        $attsql = "SELECT * FROM `overtimeattendance` WHERE `_date`='$date'";
        $attres = mysqli_query($conn, $attsql);

        if (mysqli_num_rows($attres) > 0) {
            echo "<form action='#' method='POST'>";
            echo "<table class='table table-bordered'>";
            echo "<thead><tr><th>ID</th><th>Employee Name</th><th>Date</th><th>Hours</th><th>Salary</th></tr></thead>";
            echo "<tbody>";

            while ($X = mysqli_fetch_array($attres)) {
                $salary = 0.0;
                $psalary = 125;
                $dsalary = 150;
                $whour = $X['Workhours'];
                if ($X['Worktype'] == 'painting') {
                    $salary = $whour * $psalary;
                } else {
                    $salary = $whour * $dsalary;
                }

                // Fetch employee name
                $namsql = "SELECT `cust_name` FROM `customer` WHERE `cust_id`= '" . $X['Emp_Id'] . "'";
                $nameres = mysqli_query($conn, $namsql);
                $name = mysqli_fetch_array($nameres);

                echo "<tr>";
                echo "<td><input type='hidden' name='Emp_Id[]' value='" . $X['Emp_Id'] . "'>" . $X['Emp_Id'] . "</td>";
                echo "<td>" . $name['cust_name'] . "</td>";
                echo "<td><input type='hidden' name='_date[]' value='" . $X['_date'] . "'>" . $X['_date'] . "</td>";
                echo "<td>" . $whour . "</td>";
                echo "<td><input type='hidden' name='salary[]' value='" . $salary . "'>" . $salary . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            echo "
            <center>
                <input type='submit' value='Update' name='salupt' class='btn btn-primary'>
                <a href='http://localhost/project/admin/home.php' class='btn btn-primary'>Home</a>
            </center>";
            echo "</form>";
        } else {
            echo "<p>No attendance records found for this date.</p>";
        }
    }

    // Insert salary data into osalary table
    if (isset($_POST['salupt'])) {
        $empIds = $_POST['Emp_Id'];
        $dates = $_POST['_date'];
        $salaries = $_POST['salary'];

        foreach ($empIds as $index => $empId) {
            $date = $dates[$index];
            $salary = $salaries[$index];
            $insql = "INSERT INTO `osalary` (`Emp_Id`, `_date`, `overtime_salary`) VALUES ('$empId', '$date', '$salary')";
            $inres = mysqli_query($conn, $insql);

            if (!$inres) {
                die("Error inserting overtime salary: " . mysqli_error($conn));
            }
        }
        header("Location: http://localhost/project/admin/home.php");
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainDatePicker = document.getElementById('mainDatePicker');
        mainDatePicker.value = new Date().toISOString().split('T')[0];
    });
</script>
</body>
</html>
