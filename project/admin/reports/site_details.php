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

// Get site_id from URL
$site_id = isset($_GET['site_id']) ? $_GET['site_id'] : 0;

// Fetch site details for the selected site
$site_sql = "SELECT * FROM `bookingdetails` WHERE `id` = ?";
$site_stmt = mysqli_prepare($con, $site_sql);

if (!$site_stmt) {
    die("Error preparing statement: " . mysqli_error($con));
}

mysqli_stmt_bind_param($site_stmt, "i", $site_id);
mysqli_stmt_execute($site_stmt);
$site_result = mysqli_stmt_get_result($site_stmt);

if (!$site_result) {
    die("Error executing statement: " . mysqli_error($con));
}

$site_details = mysqli_fetch_assoc($site_result);

// Fetch all payment entries for the selected site
$payment_sql = "SELECT * FROM `payments` WHERE `site_id` = ?";
$payment_stmt = mysqli_prepare($con, $payment_sql);

if (!$payment_stmt) {
    die("Error preparing payment statement: " . mysqli_error($con));
}

mysqli_stmt_bind_param($payment_stmt, "i", $site_id);
mysqli_stmt_execute($payment_stmt);
$payment_result = mysqli_stmt_get_result($payment_stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Container Styling */
        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Header Styling */
        .header {
            text-align: center;
            color: #333;
            background-color: #007bff;
            color: #fff;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }

        /* Table Styling */
        .table {
            margin-top: 20px;
            background-color: #fff;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table th {
            background-color: #007bff;
            color: #fff;
        }

        /* Button Styling */
        .btn-back {
            margin-top: 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            text-transform: uppercase;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn-back:hover {
            background-color: #0056b3;
            color: #fff;
        }

        /* Alert Styling */
        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h3>Details for Site ID: <?php echo htmlspecialchars($site_id); ?></h3>
        </div>
        
        <?php if ($site_details): ?>
            <!-- Display Site Details -->
            <table class="table table-bordered">
                <tr><th>Site Owner Name</th><td><?php echo htmlspecialchars($site_details['Name']); ?></td></tr>
                <tr><th>Location</th><td><?php echo htmlspecialchars($site_details['address']); ?></td></tr>
                <!-- Add more fields as needed -->
            </table>
        <?php else: ?>
            <p class='alert alert-warning'>No details found for this site.</p>
        <?php endif; ?>

        <h4 class="text-center mt-4">Payment Entries</h4>
        <?php
        if (mysqli_num_rows($payment_result) > 0) {
            echo "<table class='table table-bordered'>";
            echo "<thead><tr><th>Date</th><th>Total Amount</th><th>Total</th></tr></thead>";
            echo "<tbody>";
            while ($payment = mysqli_fetch_assoc($payment_result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($payment['date']) . "</td>";
                echo "<td>" . htmlspecialchars($payment['amount']) . "</td>";
                echo "<td>" . htmlspecialchars($payment['total']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='alert alert-warning'>No payment entries found for this site.</p>";
        }
        ?>
        
        <a href="pyntrep.php" class="btn btn-primary btn-back">Back to Report</a>
    </div>
</body>
</html>
