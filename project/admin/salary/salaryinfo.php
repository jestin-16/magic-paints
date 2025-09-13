<?php
$conn=mysqli_connect("localhost","root","");
if(!$conn){
    die("connection error");
}
mysqli_select_db($conn,'projectm');

// Fetch all workers
$sql="SELECT cust_id,cust_name FROM customer WHERE role='worker'";
$res=mysqli_query($conn,$sql);
if(!$res){
    die("Error: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Salary</title>
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
    <form action="#" method="POST">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Employee Name</th>
                    <th>Salary</th>
                </tr>
            </thead>
            <tbody id="attendanceBody">
                <?php
                while ($r = mysqli_fetch_array($res)) {
                    $cust_id = $r['cust_id'];
                    $cust_name = $r['cust_name'];
                    $salarySql = "SELECT Emp_Salary FROM salary WHERE Emp_id='$cust_id'";
                    $salaryRes = mysqli_query($conn, $salarySql);
                    $salary = 0;

                    if (mysqli_num_rows($salaryRes) > 0) {
                        $salaryRow = mysqli_fetch_assoc($salaryRes);
                        $salary = $salaryRow['Emp_Salary'];
                    }

                    echo "<tr>
                        <td>{$cust_id}</td>
                        <td>
                            {$cust_name}
                            <input type='hidden' name='employee_name[]' value='{$cust_name}'>
                            <input type='hidden' name='cust_id[]' value='{$cust_id}'>
                        </td>
                        <td>
                            <input type='text' name='salary_amount[]' value='{$salary}'>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
        <center>
        
          <a href="http://localhost/project/admin/home.php" class="btn btn-primary">Home</a>
       
        <input type="submit" value="Update" name="salary" class="btn btn-primary">
    
    </center>
    </form>
    <?php
    if (isset($_POST['salary'])) {
        $employeeIds = $_POST['cust_id'] ?? [];
        $salaries = $_POST['salary_amount'] ?? [];

        foreach ($employeeIds as $index => $cust_id) {
            $salary = $salaries[$index];

            
            $checksalsql = "SELECT * FROM salary WHERE Emp_id='$cust_id'";
            $checkressal = mysqli_query($conn, $checksalsql);

            if(mysqli_num_rows($checkressal) == 0){
               
                $insertsql = "INSERT INTO salary (Emp_Id, Emp_Salary) VALUES ('$cust_id', '$salary')";
                mysqli_query($conn, $insertsql);
            } else {
                
                $updatesql = "UPDATE salary SET Emp_Salary='$salary' WHERE Emp_id='$cust_id'";
                mysqli_query($conn, $updatesql);
            }
        }
    }
    ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/js/bootstrap.min.js"></script>
</body>
</html>
