<?php
$conn = mysqli_connect("localhost", "root", "");
if (!$conn) {
    die("Connection error: " . mysqli_connect_error());
}
mysqli_select_db($conn, 'projectm');

$sql = "SELECT cust_id, cust_name FROM customer WHERE role='worker'";
$res = mysqli_query($conn, $sql);
if (!$res) {
    die("Error fetching workers: " . mysqli_error($conn));
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
        echo "<h4>Attendance for: " . htmlspecialchars($date) . "</h4>";

        $attsql = "SELECT * FROM `attendance` WHERE `_date`='$date'";
        $attres = mysqli_query($conn, $attsql);
        if (mysqli_num_rows($attres) > 0) {
            echo "<form action='#' method='POST'>";
            echo "<table class='table table-bordered'>";
            echo "<thead><tr><th>ID</th><th>Employee Name</th><th>Work Type</th><th>Date</th><th>Salary</th></tr></thead>";
            echo "<tbody>";

            while ($X = mysqli_fetch_array($attres)) {
                // Fetch the salary from the salary table based on Emp_Id
                $atsql = "SELECT `Emp_Salary` FROM `salary` WHERE `Emp_Id`='$X[Emp_Id]'";
                $atres = mysqli_query($conn, $atsql);
                $a = mysqli_fetch_array($atres);

                // Calculate salary based on work type and session attendance
                $salary = 0.0;
                $psalary = 1000; // Painting salary
                $dsalary = 1800; // Design or Texture salary

                if ($X['Worktype'] == 'painting') {
                    if ($X['Session1'] == 1 && $X['Session2'] == 1) {
                        $salary = $psalary;
                    } elseif ($X['Session1'] == 1 || $X['Session2'] == 1) {
                        $salary = $psalary / 2.0;
                    }
                } else if ($X['Worktype'] == 'design' || $X['Worktype'] == 'Texture') {
                    if ($X['Session1'] == 1 && $X['Session2'] == 1) {
                        $salary = $dsalary;
                    } elseif ($X['Session1'] == 1 || $X['Session2'] == 1) {
                        $salary = $dsalary / 2.0;
                    }
                }

                // Fetch employee name
                $namsql = "SELECT `cust_name` FROM `customer` WHERE `cust_id`= '$X[Emp_Id]'";
                $nameres = mysqli_query($conn, $namsql);
                $name = mysqli_fetch_array($nameres);

                // Display the row with employee details and salary
                echo "<tr>";
                echo "<td><input type='hidden' name='Emp_Id[]' value='" . htmlspecialchars($X['Emp_Id']) . "'>" . htmlspecialchars($X['Emp_Id']) . "</td>";
                echo "<td>" . htmlspecialchars($name['cust_name']) . "</td>";
                echo "<td>" . htmlspecialchars($X['Worktype']) . "</td>";
                echo "<td><input type='hidden' name='_date[]' value='" . htmlspecialchars($X['_date']) . "'>" . htmlspecialchars($X['_date']) . "</td>";
                echo "<td><input type='hidden' name='salary[]' value='" . htmlspecialchars($salary) . "'>" . htmlspecialchars($salary) . "</td>";
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

    if (isset($_POST['salupt'])) {
        $empIds = $_POST['Emp_Id'];
        $dates = $_POST['_date'];
        $salaries = $_POST['salary'];

        foreach ($empIds as $index => $empId) {
            $date = $dates[$index];
            $salary = $salaries[$index];

            // Insert into dsalary table
            $insql = "INSERT INTO `dsalary` (`Emp_Id`, `_date`, `dailysalary`) VALUES ('$empId', '$date', '$salary')";
            $inres = mysqli_query($conn, $insql);
            if (!$inres) {
                die("Error inserting data for employee ID $empId: " . mysqli_error($conn));
            }
        }
        // Redirect to home after successful insertion
        header("Location: http://localhost/project/admin/home.php");
        exit();
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainDatePicker = document.getElementById('mainDatePicker');
        const datePickers = document.querySelectorAll('.date-picker');

        const setAllDates = (date) => {
            datePickers.forEach(picker => picker.value = date);
        };

        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        const today = new Date();
        const todayFormatted = formatDate(today);
        mainDatePicker.value = todayFormatted;
        setAllDates(todayFormatted);

        mainDatePicker.addEventListener('change', (event) => {
            setAllDates(event.target.value);
        });
    });
</script>
</body>
</html>
