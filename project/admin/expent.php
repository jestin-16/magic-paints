<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "projectm"; // Replace with your actual database name

// Create connection
$con = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch ongoing work details
$bsql = "SELECT `id`, `Name` FROM `bookingdetails` WHERE `status` LIKE '%ongoing%'";
$bres = mysqli_query($con, $bsql);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Convert date format to 'YYYY-MM-DD'
    $attdate = date("Y-m-d", strtotime($_POST['attdate']));
    
    $site_id = $_POST['site_id'];
    $travel_amount = $_POST['tamount'];
    $rent_amount = $_POST['ramount'];
    $total = $travel_amount + $rent_amount; // Calculate total

    // Validate form inputs
    if (empty($attdate) || empty($travel_amount) || empty($rent_amount) || empty($site_id)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($travel_amount) || !is_numeric($rent_amount)) {
        $error = "Amount fields must be valid numbers.";
    } else {
        // Debug output to check date format
        // echo "Formatted Date: $attdate"; 
        // For debugging purposes

        // Insert payment details into the expenses table
        $sql = "INSERT INTO `expenses`(`site_id`, `t_amnt`, `r_amnt`, `_date`, `_tot`) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "iiiss", $site_id, $travel_amount, $rent_amount, $attdate, $total);

        if (mysqli_stmt_execute($stmt)) {
            // Check for successful insertion
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $success = "Payment and expense details successfully added!";
            } else {
                $error = "Failed to insert payment details.";
            }
        } else {
            $error = "Database error: " . mysqli_error($con); // Capture any SQL error
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-control, .form-select {
            width: 100%;
        }
        .btn {
            width: 150px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <h2>Expense Entry</h2>
        <form action="#" method="POST">
            <div class="mb-3">
                <label for="mainDatePicker" class="form-label">Select Date:</label>
                <input type="date" id="mainDatePicker" class="form-control" name="attdate" required>
            </div>
            <div class="mb-3">
                <label for="siteSelect" class="form-label">Select Site:</label>
                <select name="site_id" id="siteSelect" class="form-select" required>
                    <option value="">Select Site</option>
                    <?php
                    // Loop through ongoing works to populate the Site dropdown
                    while ($rs = mysqli_fetch_assoc($bres)) {
                        echo "<option value='{$rs['id']}'>{$rs['Name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="tamount" class="form-label">Enter Travel Amount:</label>
                <input type="text" id="tamount" class="form-control" name="tamount" placeholder="e.g., 500" required>
            </div>
            <div class="mb-3">
                <label for="ramount" class="form-label">Enter Rent Amount:</label>
                <input type="text" id="ramount" class="form-control" name="ramount" placeholder="e.g., 1000" required>
            </div>
            <div class="mb-3">
                <label for="ramount" class="form-label">Enter Materials Amount:</label>
                <input type="text" id="mamount" class="form-control" name="mamount" placeholder="e.g., 1000" required>
            </div>
            <div class="text-center">
                <a href="http://localhost/project/admin/home.php" class="btn btn-primary">Home</a>
                <input type="submit" value="Submit Payment" class="btn btn-success">
            </div>
        </form>
    </div>

    <!-- JavaScript to handle pushState and popState -->
    <script>
        window.history.pushState({page: 1}, "", ""); 

        window.onpopstate = function (event) {
            if (event.state) {
                window.location.href = "http://localhost/project/admin/home.php";
            }
        };

        window.onload = function () {
            if (!window.history.state) {
                window.history.replaceState({page: 1}, "", "");
            }
        };
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
