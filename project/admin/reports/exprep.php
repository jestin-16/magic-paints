<?php
$servername = "localhost";
$username = "root";
$password = "";
$con = mysqli_connect($servername, $username, $password);

if (!$con) {
    die("Connection error: " . mysqli_connect_error());
}

$dbname = "projectm";
mysqli_select_db($con, $dbname);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f8fa;
        }
        .container-title {
            text-align: center;
            padding: 20px 0;
            background-color: #343a40;
            color: white;
            margin-bottom: 20px;
        }
        .form-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-container button {
            padding: 10px 20px;
            background-color: #007bff;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
        }
        .form-container button:hover {
            background-color: #0056b3;
        }
        .table-container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .btn-view {
            padding: 8px 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container-title">
        <h3>Payment Report</h3>
    </div>

    <div class="container">
        <div class="form-container">
            <a href="http://localhost/project/admin/home.php" class="btn btn-primary">Home</a>
            <button type="button" onclick="generatePDF()" class="btn btn-success">Export to PDF</button>
        </div>

        <?php
        $sql = "SELECT * FROM `expenses`";
        // SELECT `exp_id`, `site_id`, `t_amnt`, `r_amnt`, `_date`, `_tot` FROM `expenses` WHERE 1
        $res = mysqli_query($con, $sql);

        if (!$res) {
            echo "<div class='container'>";
            echo "<p class='alert alert-danger mt-5'>Error executing query: " . mysqli_error($con) . "</p>";
            echo "</div>";
        } elseif (mysqli_num_rows($res) > 0) {
            echo "<div class='container'>";
            echo "<div class='table-responsive'>";
            echo "<table id='attendanceTable' class='table table-bordered table-striped'>";
            echo "<thead><tr> <th>expid</th><th>siteid</th><th>tamount</th><th>ramount</th><th>date</th><th>total</th></tr></thead>";
            echo "<tbody>";
            while ($x = mysqli_fetch_array($res)) {
                echo "<tr>";
                echo "<td>" . $x['exp_id'] . "</td>";
                
                echo "<td>" . $x['site_id'] . "</td>";
                echo "<td>" . $x['t_amnt'] . "</td>";
                echo "<td>" . $x['r_amnt'] . "</td>";
                echo "<td>" . $x['_date'] . "</td>";
                echo "<td>" . $x['_tot'] . "</td>";
                
                
                
                echo "</tr>";
            }
            echo "</tbody></table></div></div>";
        } else {
            echo "<div class='container'><p class='alert alert-warning mt-5'>No records found for the selected date.</p></div>";
        }
        ?>
    </div>
</body>
</html>
