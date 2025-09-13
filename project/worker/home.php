<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
try {
    $pdo = new PDO("mysql:host=$servername;dbname=projectm", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch cust_id and store in session
if (!isset($_SESSION['cust_id'])) {
    $stmt = $pdo->prepare("SELECT cust_id FROM customer WHERE cust_name = ?");
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['cust_id'] = $user['cust_id'];
}

// Handle Worker Info Update
if (isset($_POST['update_worker'])) {
    $age = $_POST['age'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    
    // Update customer table
    $stmt_customer = $pdo->prepare("UPDATE customer SET cust_name = ?, cust_ph = ? WHERE cust_id = ?");
    $stmt_customer->execute([$name, $phone, $_SESSION['cust_id']]);
    
    // Update session username if name changed
    if ($_SESSION['username'] !== $name) {
        $_SESSION['username'] = $name;
    }

    // Check and update worker info
    $stmt_check = $pdo->prepare("SELECT * FROM worker WHERE cust_id = ?");
    $stmt_check->execute([$_SESSION['cust_id']]);

    if ($stmt_check->rowCount() > 0) {
        $stmt_update = $pdo->prepare("UPDATE worker SET age = ?, email = ?, address = ? WHERE cust_id = ?");
        $stmt_update->execute([$age, $email, $address, $_SESSION['cust_id']]);
    } else {
        $stmt_insert = $pdo->prepare("INSERT INTO worker (cust_id, age, email, address) VALUES (?, ?, ?, ?)");
        $stmt_insert->execute([$_SESSION['cust_id'], $age, $email, $address]);
    }
    
    // Handle password update if provided
    if (!empty($_POST['new_password'])) {
        if (strlen($_POST['new_password']) < 6) {
            $error_message = "Password must be at least 6 characters long!";
        } else {
            $hashed_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $stmt_password = $pdo->prepare("UPDATE customer SET password = ? WHERE cust_id = ?");
            $stmt_password->execute([$hashed_password, $_SESSION['cust_id']]);
        }
    }
    
    $success_message = "Information updated successfully!";
}

// Add new password change handler
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $stmt = $pdo->prepare("SELECT password FROM customer WHERE cust_id = ?");
    $stmt->execute([$_SESSION['cust_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!password_verify($current_password, $user['password'])) {
        $password_error = "Current password is incorrect!";
    } elseif ($new_password !== $confirm_password) {
        $password_error = "New passwords do not match!";
    } elseif (strlen($new_password) < 6) {
        $password_error = "Password must be at least 6 characters long!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE customer SET password = ? WHERE cust_id = ?");
        $stmt->execute([$hashed_password, $_SESSION['cust_id']]);
        $password_success = "Password updated successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --light-bg: #f8f9fa;
            --dark-bg: #343a40;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
        }

        .sidebar {
            background-color: var(--dark-bg);
            min-height: 100vh;
            padding: 20px;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 10px 0;
            padding: 12px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .sidebar a:hover {
            background-color: var(--primary-color);
            transform: translateX(5px);
        }

        .content-section {
            display: none;
            padding: 25px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin: 20px 0;
        }

        .content-section.active {
            display: block;
        }

        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .table {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead {
            background-color: var(--dark-bg);
            color: white;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 10px 20px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 8px 12px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
    </style>
</head>
<body>
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Navbar -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand">Worker Dashboard</span>
            <div>
                <span class="text-light me-3">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a class="btn btn-light" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <a href="#" onclick="showSection('profile')">Profile</a>
                <a href="#" onclick="showSection('attendance')">View Attendance</a>
                <a href="#" onclick="showSection('salary')">View Salary</a>
                <a href="#" onclick="showSection('overtime_attendance')">Overtime Attendance</a>
                <a href="#" onclick="showSection('overtime_salary')">Overtime Salary</a>
                <a href="#" onclick="showSection('update_info')">Update Personal Info</a>
                <a href="#" onclick="showSection('change_password')">Change Password</a>
            </div>

            <!-- Content -->
            <div class="col-md-10 py-3">
                <!-- Profile Section -->
                <div id="profile" class="content-section active">
                    <h2 class="mb-4">Profile Information</h2>
                    <div class="card">
                        <div class="card-body">
                            <?php 
                                $stmt = $pdo->prepare("SELECT c.cust_name, c.cust_ph, c.role, w.age, w.email, w.address 
                                                    FROM customer c 
                                                    LEFT JOIN worker w ON c.cust_id = w.cust_id 
                                                    WHERE c.cust_name = ?");
                                $stmt->execute([$_SESSION['username']]);
                                $profile = $stmt->fetch(PDO::FETCH_ASSOC);

                                if ($profile) {
                                    echo "<p><strong>Name:</strong> " . htmlspecialchars($profile['cust_name']) . "</p>";
                                    echo "<p><strong>Phone:</strong> " . htmlspecialchars($profile['cust_ph']) . "</p>";
                                    echo "<p><strong>Role:</strong> " . htmlspecialchars($profile['role']) . "</p>";
                                    echo "<p><strong>Age:</strong> " . htmlspecialchars($profile['age'] ?? 'N/A') . "</p>";
                                    echo "<p><strong>Email:</strong> " . htmlspecialchars($profile['email'] ?? 'N/A') . "</p>";
                                    echo "<p><strong>Address:</strong> " . htmlspecialchars($profile['address'] ?? 'N/A') . "</p>";
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Attendance Section -->
                <div id="attendance" class="content-section">
                    <h2 class="mb-4">Regular Attendance</h2>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Work Type</th>
                                    <th>Session 1</th>
                                    <th>Session 2</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $stmt = $pdo->prepare("SELECT _date, Worktype, Session1, Session2 
                                                        FROM attendance 
                                                        WHERE Emp_Id = ?");
                                    $stmt->execute([$_SESSION['cust_id']]);
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['_date']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['Worktype']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['Session1']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['Session2']) . "</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Salary Section -->
                <div id="salary" class="content-section">
                    <h2 class="mb-4">Regular Salary</h2>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Daily Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $stmt = $pdo->prepare("SELECT _date, dailysalary 
                                                        FROM dsalary 
                                                        WHERE Emp_Id = ?");
                                    $stmt->execute([$_SESSION['cust_id']]);
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['_date']) . "</td>";
                                        echo "<td>₹" . htmlspecialchars($row['dailysalary']) . "</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Overtime Attendance Section -->
                <div id="overtime_attendance" class="content-section">
                    <h2 class="mb-4">Overtime Attendance</h2>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Work Type</th>
                                    <th>Work Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $stmt = $pdo->prepare("SELECT _date, Worktype, Workhours 
                                                        FROM overtimeattendance 
                                                        WHERE Emp_Id = ?");
                                    $stmt->execute([$_SESSION['cust_id']]);
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['_date']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['Worktype']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['Workhours']) . "</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Overtime Salary Section -->
                <div id="overtime_salary" class="content-section">
                    <h2 class="mb-4">Overtime Salary</h2>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Overtime Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $stmt = $pdo->prepare("SELECT _date, overtime_salary 
                                                        FROM osalary 
                                                        WHERE Emp_Id = ?");
                                    $stmt->execute([$_SESSION['cust_id']]);
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['_date']) . "</td>";
                                        echo "<td>₹" . htmlspecialchars($row['overtime_salary']) . "</td>";
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Update Personal Info Section -->
                <div id="update_info" class="content-section">
                    <h2 class="mb-4">Update Personal Information</h2>
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                            <?php endif; ?>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($profile['cust_name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($profile['cust_ph']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="age" class="form-label">Age</label>
                                    <input type="number" class="form-control" name="age" value="<?php echo htmlspecialchars($profile['age'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" name="address" rows="3" required><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                                </div>
                                <!-- <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password (leave blank to keep current)</label>
                                    <input type="password" class="form-control" name="new_password" minlength="6">
                                </div> -->
                                <button type="submit" name="update_worker" class="btn btn-primary">Update Information</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Change Password Section -->
                <div id="change_password" class="content-section">
                    <h2 class="mb-4">Change Password</h2>
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($password_error)): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($password_error); ?></div>
                            <?php endif; ?>
                            <?php if (isset($password_success)): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($password_success); ?></div>
                            <?php endif; ?>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" name="new_password" minlength="6" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" name="confirm_password" minlength="6" required>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showSection(section) {
            document.querySelectorAll('.content-section').forEach(function(sec) {
                sec.classList.remove('active');
            });
            document.getElementById(section).classList.add('active');
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>