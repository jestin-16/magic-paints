<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "projectm");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$cust_name = $_SESSION['username'];

// Fetch profile details
$sql = "SELECT cust_id, cust_name, cust_ph, role FROM customer WHERE cust_name = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $cust_name);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

// Fetch booking details (Orders)
$bsql = "SELECT `id`, `username`, `Name`, `phoneNo`, `address`, `location`, `build_type`, `work_type`, `sqfeet`, `status`, `created_at` FROM `bookingdetails` WHERE `username` = ?";
$bstmt = $con->prepare($bsql);
$bstmt->bind_param("s", $cust_name);
$bstmt->execute();
$bookingResult = $bstmt->get_result();

// Fetch messages and their responses
$message_sql = "SELECT m.id AS message_id, m.message, m.sender, m.created_at, 
                       r.response AS response_text, r.response_date
                FROM messages m
                LEFT JOIN message_responses r ON m.id = r.message_id
                WHERE m.user_id = ?";
$message_stmt = $con->prepare($message_sql);
$message_stmt->bind_param("i", $profile['cust_id']);
$message_stmt->execute();
$message_result = $message_stmt->get_result();

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['send_message'])) {
        $message = $_POST['message'];

        // Insert the message into the messages table
        $message_sql = "INSERT INTO messages (user_id, message, sender) VALUES (?, ?, 'user')";
        $message_stmt = $con->prepare($message_sql);
        $message_stmt->bind_param("is", $profile['cust_id'], $message);
        $message_stmt->execute();

        $message_success = "Your message has been sent successfully!";
    }

    if (isset($_POST['update_profile'])) {
        $new_name = $_POST['name'];
        $new_phone = $_POST['phone'];

        $update_sql = "UPDATE customer SET cust_name = ?, cust_ph = ? WHERE cust_id = ?";
        $update_stmt = $con->prepare($update_sql);
        $update_stmt->bind_param("ssi", $new_name, $new_phone, $profile['cust_id']);
        $update_stmt->execute();

        $_SESSION['username'] = $new_name;
        header("Location: http://localhost/project/home/login.php");
        exit();
    }

    if (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];

        $pass_sql = "SELECT Password FROM customer WHERE cust_id = ?";
        $pass_stmt = $con->prepare($pass_sql);
        $pass_stmt->bind_param("i", $profile['cust_id']);
        $pass_stmt->execute();
        $pass_result = $pass_stmt->get_result();
        $user = $pass_result->fetch_assoc();

        if (password_verify($current_password, $user['Password'])) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $update_pass_sql = "UPDATE customer SET Password = ? WHERE cust_id = ?";
            $update_pass_stmt = $con->prepare($update_pass_sql);
            $update_pass_stmt->bind_param("si", $new_password_hash, $profile['cust_id']);
            $update_pass_stmt->execute();
            $password_message = "Password updated successfully!";
        } else {
            $password_message = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        /* Dashboard styling */
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand, .nav-link {
            color: #ffffff !important;
        }
        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
            padding-top: 20px;
        }
        .sidebar-link {
            display: block;
            padding: 15px;
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background-color: #495057;
        }
        .content {
            background-color: #ffffff;
            border-left: 1px solid #dee2e6;
        }
        .content-section {
            display: none;
            padding: 20px;
            border-radius: 8px;
            background-color: #f1f1f1;
        }
        .content-section.active {
            display: block;
        }
        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-container h3 {
            color: #343a40;
        }
        .form-container button {
            margin-top: 10px;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                width: 100%;
                top: 56px;
                height: auto;
            }
            .sidebar-link {
                text-align: center;
                padding: 10px;
            }
            .content {
                margin-top: 140px;
            }
        }
        .avatar-circle {
            width: 150px;
            height: 150px;
            background-color: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
        
        .profile-icon {
            width: 40px;
            text-align: center;
        }
        
        .list-group-item {
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            transition: transform 0.2s ease-in-out;
        }
        
        .list-group-item:hover {
            transform: translateX(5px);
            background-color: #f8f9fa;
        }
        
        .card.shadow {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        .card-header {
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
        }

        /* Enhanced table styles */
        .table {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead {
            background-color: #0d6efd;
            color: white;
        }

        .table th {
            padding: 12px;
            font-weight: 500;
        }

        /* Form styling */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 6px;
            padding: 10px;
            border: 1px solid #ced4da;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        /* Button styling */
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
        }

        /* Alert styling */
        .alert {
            border-radius: 6px;
            padding: 12px 20px;
            margin: 15px 0;
        }

        /* Card enhancements */
        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        /* Section styling */
        .content-section {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .content-section h2 {
            color: #0d6efd;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        /* Sidebar enhancements */
        .sidebar-link {
            margin: 5px 15px;
            border-radius: 6px;
        }

        .sidebar-link:hover {
            background-color: #0d6efd;
            color: white;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .content-section {
                padding: 15px;
            }
            
            .table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">User Dashboard</a>
            <div class="collapse navbar-collapse justify-content-end">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost/project/home/login.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 sidebar">
                <a href="#" onclick="showSection('profile')" class="sidebar-link">Profile</a>
                <a href="#" onclick="showSection('orders')" class="sidebar-link">Bookings</a>
                <a href="#" onclick="showSection('edit_profile')" class="sidebar-link">Edit Profile</a>
                <a href="#" onclick="showSection('update_password')" class="sidebar-link">Change Password</a>
                <a href="#" onclick="showSection('send_message')" class="sidebar-link">Send Message</a>
                <a href="#" onclick="showSection('messages')" class="sidebar-link">View Messages</a>
                <a href="http://localhost/project/user/home.php" class="sidebar-link" >Home</a>
            </div>

            <div class="col-md-9 content p-4">
                <!-- Profile Section -->
                <div id="profile" class="content-section active">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="mb-0">Profile Information</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($profile): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table">
                                            <tr>
                                                <th width="150">Name:</th>
                                                <td><?php echo htmlspecialchars($profile['cust_name']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Phone:</th>
                                                <td><?php echo htmlspecialchars($profile['cust_ph']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Role:</th>
                                                <td><?php echo htmlspecialchars($profile['role']); ?></td>
                                            </tr>
                                        </table>
                                        <div class="mt-3">
                                            <button class="btn btn-primary" onclick="showSection('edit_profile')">
                                                Edit Profile
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    Profile details not found.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Orders Section -->
                <div id="orders" class="content-section">
                    <h2>Orders</h2>
                    <?php if ($bookingResult->num_rows > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone No</th>
                                    <th>Address</th>
                                    <th>Location</th>
                                    <th>Build Type</th>
                                    <th>Work Type</th>
                                    <th>Sq. Feet</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($booking = $bookingResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['Name']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['phoneNo']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['address']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['location']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['build_type']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['work_type']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['sqfeet']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['created_at']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No orders found.</p>
                    <?php endif; ?>
                </div>

                <!-- View Messages Section -->
                <div id="messages" class="content-section">
                    <h2>View Messages</h2>
                    <?php if ($message_result->num_rows > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Message</th>
                                    <th>Sender</th>
                                    <th>Created At</th>
                                    <th>Response</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($message = $message_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($message['message_id']); ?></td>
                                        <td><?php echo htmlspecialchars($message['message']); ?></td>
                                        <td><?php echo htmlspecialchars($message['sender']); ?></td>
                                        <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                                        <td>
                                            <?php if ($message['response_text']): ?>
                                                <strong>Response:</strong> <?php echo htmlspecialchars($message['response_text']); ?> <br>
                                                <em>On: <?php echo htmlspecialchars($message['response_date']); ?></em>
                                            <?php else: ?>
                                                <em>No response yet.</em>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No messages found.</p>
                    <?php endif; ?>
                </div>

                <!-- Send Message Section -->
                <div id="send_message" class="content-section">
                    <h2>Send Message</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="message">Your Message</label>
                            <textarea name="message" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" name="send_message" class="btn btn-primary mt-2">Send Message</button>
                    </form>
                    <?php if (isset($message_success)): ?>
                        <div class="alert alert-success mt-3">
                            <?php echo $message_success; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Edit Profile Section -->
                <div id="edit_profile" class="content-section">
                    <h2>Edit Profile</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($profile['cust_name']); ?>" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($profile['cust_ph']); ?>" class="form-control" required>
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary mt-2">Update Profile</button>
                    </form>
                </div>

                <!-- Update Password Section -->
                <div id="update_password" class="content-section">
                    <h2>Change Password</h2>
                    <form method="POST" action="" id="passwordForm" onsubmit="return validatePassword()">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password" class="form-control" required>
                                <!-- <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="bi bi-eye"></i>
                                </button> -->
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label for="new_password">New Password</label>
                            <div class="input-group">
                                <input type="password" name="new_password" id="new_password" class="form-control" 
                                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" 
                                    title="Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character"
                                    required>
                                <!-- <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="bi bi-eye"></i>
                                </button> -->
                            </div>
                            <small class="text-muted">Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.</small>
                        </div>
                        <div class="form-group mt-3">
                            <label for="confirm_password">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                <!-- <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                    <i class="bi bi-eye"></i>
                                </button> -->
                            </div>
                        </div>
                        <button type="submit" name="update_password" class="btn btn-primary mt-3">Change Password</button>
                    </form>
                    <?php if (isset($password_message)): ?>
                        <div class="alert alert-info mt-3">
                            <?php echo $password_message; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            document.querySelectorAll('.content-section').forEach(function(section) {
                section.classList.remove('active');
            });
            document.getElementById(sectionId).classList.add('active');
        }

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        function validatePassword() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                alert('New password and confirm password do not match!');
                return false;
            }

            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            if (!passwordRegex.test(newPassword)) {
                alert('Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character');
                return false;
            }

            return confirm('Are you sure you want to change your password?');
        }
    </script>
</body>
</html>
