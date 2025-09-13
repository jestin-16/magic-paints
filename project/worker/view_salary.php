<?php 
session_start();

if (!isset($_SESSION["username"])) {
  header("Location: login.php");
  exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$con = mysqli_connect($servername, $username, $password);
if (!$con) {
  die("connection error");
}

$dbname = "projectm";
mysqli_select_db($con, $dbname);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Worker Home Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f8f9fa;
    }

    .container {
      margin-top: 50px;
      max-width: 800px;
      background-color: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h3 {
      text-align: center;
      margin-bottom: 30px;
      color: #343a40;
      font-weight: 700;
    }

    .btn-custom {
      background-color: #007bff;
      border-color: #007bff;
      color: white;
      padding: 10px 16px;
      border-radius: 4px;
      font-weight: 500;
      margin-bottom: 10px;
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    .btn-custom:hover {
      background-color: #0056b3;
      box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .btn-danger {
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h3>Welcome</h3>
    <div class="mb-4">
      <?php 
      $sql = "SELECT `cust_name`, `cust_ph`, `role` FROM `customer` WHERE `cust_name`='$_SESSION[username]'";
      $res = mysqli_query($con, $sql);
      while ($x = mysqli_fetch_array($res)) {
        echo "<p><strong>Name:</strong> " . $x['cust_name'] . "</p>";
        echo "<p><strong>Phone:</strong> " . $x['cust_ph'] . "</p>";
        echo "<p><strong>Role:</strong> " . $x['role'] . "</p>";
      }
      ?>
    </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php

$sql = "SELECT `cust_id`, `cust_name`, `cust_ph`, `role` FROM `customer` WHERE `cust_name`='$_SESSION[username]'";
$res = mysqli_query($con, $sql);
$x = mysqli_fetch_array($res);
$name=$x['cust_name'];
$id=$x['cust_id'];
echo $name;


$sql = "SELECT `Emp_Id`, `_date`, `dailysalary` FROM `dsalary` WHERE `Emp_Id` = '$id'";
    $res = mysqli_query($con, $sql);

    if (mysqli_num_rows($res) > 0) {
        echo "<div class='container'>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Employee ID</th>";
        echo "<th>Date</th>";
        echo "<th>Salary</th>";
    
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($x = mysqli_fetch_array($res)) {
            $namsql="SELECT `cust_name` FROM `customer` WHERE `cust_id`= '$x[Emp_Id]'";
                $nameres=mysqli_query($con,$namsql);
                $name=mysqli_fetch_array($nameres);
            echo "<tr>";
            echo "<td>".$name['cust_name']."</td>";
            echo "<td>" . $x['_date'] . "</td>";
            echo "<td>" . $x['dailysalary'] . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        echo "</div>";
    }


?>