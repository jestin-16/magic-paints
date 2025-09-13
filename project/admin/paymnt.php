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
    $attdate = $_POST['attdate'];
    $amount = $_POST['amount'];
    $site_id = $_POST['site_id'];

    // Validate form inputs
    if (empty($attdate) || empty($amount) || empty($site_id)) {
        $error = "All fields are required.";
    } elseif (!is_numeric($amount)) {
        $error = "Amount must be a valid number.";
    } else {
        // Retrieve the current total for this site_id
        $totalQuery = "SELECT SUM(`amount`) AS `current_total` FROM `payments` WHERE `site_id` = ?";
        $totalStmt = mysqli_prepare($con, $totalQuery);
        mysqli_stmt_bind_param($totalStmt, "i", $site_id);
        mysqli_stmt_execute($totalStmt);
        mysqli_stmt_bind_result($totalStmt, $currentTotal);
        mysqli_stmt_fetch($totalStmt);
        mysqli_stmt_close($totalStmt);

        // Calculate the new total by adding the current amount
        $newTotal = $currentTotal + $amount;

        // Insert payment details into the payments table
        $sql = "INSERT INTO `payments` (`date`, `amount`, `site_id`, `total`) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sdid", $attdate, $amount, $site_id, $newTotal);
        mysqli_stmt_execute($stmt);

        // Check for successful insertion
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $success = "Payment details successfully added!";
        } else {
            $error = "Failed to insert payment details.";
        }

        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-control, .form-select {
            width: 100%;
        }
        .btn {
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Payment Entry</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="mainDatePicker" class="form-label">Select Date:</label>
                <input type="date" id="mainDatePicker" class="form-control" name="attdate">
            </div>

            <div class="mb-3">
                <label for="amount" class="form-label">Enter Amount:</label>
                <input type="text" id="amount" class="form-control" name="amount">
            </div>

            <div class="mb-3">
                <label for="siteSelect" class="form-label">Select Site:</label>
                <select name="site_id" id="siteSelect" class="form-select">
                    <?php
                    // Loop through ongoing works to populate the Site dropdown
                    while ($rs = mysqli_fetch_assoc($bres)) {
                        echo "<option value='{$rs['id']}'>{$rs['Name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <center>
                <a href="http://localhost/project/admin/home.php" class="btn btn-primary">Home</a>
                <input type="submit" value="Submit Payment" class="btn btn-success">
            </center>
        </form>
    </div>

    <!-- JavaScript to handle pushState and popState -->
    <script>
        // Prevent back navigation and handle the popstate event
        window.history.pushState({page: 1}, "", ""); // Push a new state to the history stack

        window.onpopstate = function (event) {
            // Redirect to home page when the back button is pressed
            if (event.state) {
                window.location.href = "http://localhost/project/admin/home.php";
            }
        };

        // Optional: Handle additional forward/backward browser behavior
        window.onload = function () {
            if (!window.history.state) {
                window.history.replaceState({page: 1}, "", ""); // Replace the initial state if missing
            }
        };
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
