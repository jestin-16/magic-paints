<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f4f6f9;
            min-height: 100vh;
            color: #2d3436;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background-color: #ffffff;
            border-bottom: 2px solid #dfe6e9;
            color: #2d3436;
        }
        .navbar-brand {
            color: #2d3436;
            font-weight: bold;
            font-size: 1.4rem;
            letter-spacing: 1px;
        }
        .logout-link {
            color: #ffffff;
            background-color: #3498db;
            padding: 8px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.2s ease;
        }
        .logout-link:hover {
            background-color: #2980b9;
            color: #ffffff;
        }
        .sidebar {
            background-color: #ffffff;
            border-right: 2px solid #dfe6e9;
            min-height: 100vh;
            padding-top: 20px;
        }
        .sidebar a {
            color: #2d3436;
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            transition: background 0.3s, color 0.3s;
            font-size: 0.95rem;
            border-radius: 6px;
            margin: 0 10px 10px 10px;
        }
        .sidebar a:hover {
            background-color: #dfe6e9;
            color: #3498db;
        }
        .sidebar .nav-icon {
            margin-right: 10px;
            color: #3498db;
            font-size: 1.2rem;
        }
        .dashboard-card {
            background-color: #ffffff;
            border: 1px solid #dfe6e9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            border-radius: 8px;
            padding: 20px;
            color: #2d3436;
            height: 100%;
        }
        .card-icon {
            font-size: 2.5rem;
            color: #3498db;
            margin-bottom: 15px;
        }
        .dashboard-card h5 {
            font-size: 1.1rem;
            color: #2d3436;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .dashboard-card a {
            text-decoration: none;
            color: #3498db;
            font-weight: normal;
            transition: color 0.3s ease;
        }
        .dashboard-card a:hover {
            color: #2980b9;
        }
        .dashboard-card:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-3px);
        }
        .content-section {
            display: none;
            padding-top: 20px;
            margin-bottom: 30px;
        }
        .content-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #2d3436;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #dfe6e9;
            padding-bottom: 8px;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                border-right: none;
                border-bottom: 2px solid #dfe6e9;
                margin-bottom: 20px;
            }
            .content-section {
                padding-top: 10px;
            }
            .col-md-6 {
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-paint-bucket"></i> Magic Paints Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="logout-link" href="http://localhost/project/home/login.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 sidebar p-0">
                <a href="#" onclick="showSection('section1')">
                    <i class="nav-icon bi bi-house-door"></i> User Management
                </a>
                <a href="#" onclick="showSection('section2')">
                    <i class="nav-icon bi bi-cash"></i> Salary Management
                </a>
                <a href="#" onclick="showSection('section3')">
                    <i class="nav-icon bi bi-calendar-check"></i> Attendance Management
                </a>
                <a href="#" onclick="showSection('section4')">
                    <i class="nav-icon bi bi-file-text"></i> Reports
                </a>
                <a href="#" onclick="showSection('section5')">
                    <i class="nav-icon bi bi-briefcase"></i> Work Orders
                </a>
                <a href="#" onclick="showSection('section6')">
                    <i class="nav-icon bi bi-wallet2"></i> Expenses
                </a>
                <a href="#" onclick="showSection('section7')">
                    <i class="nav-icon bi bi-envelope"></i> Messages
                </a>
            </div>

            <!-- Content Area -->
            <div class="col-md-9 p-4">
                <!-- Section 1: User Management -->
                <div id="section1" class="content-section active">
                    <div class="section-title">User Management</div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-display card-icon"></i>
                                <h5>Users</h5>
                                <a href="display.php">View All Users</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-people card-icon"></i>
                                <h5>Customer Display</h5>
                                <a href="http://localhost/project/admin/cdisplay.php">View Customers</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-person-workspace card-icon"></i>
                                <h5>Workers Display</h5>
                                <a href="http://localhost/project/admin/wdisplay.php">View Workers</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Salary Management -->
                <div id="section2" class="content-section">
                    <div class="section-title">Salary Management</div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-currency-dollar card-icon"></i>
                                <h5>Salary Update</h5>
                                <a href="salary/salary.php">Update Salary Info</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-clock-history card-icon"></i>
                                <h5>Overtime Salary Update</h5>
                                <a href="salary/addsal.php">Update Overtime Salary</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Attendance Management -->
                <div id="section3" class="content-section">
                    <div class="section-title">Attendance Management</div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-calendar-check card-icon"></i>
                                <h5>Attendance Management</h5>
                                <a href="http://localhost/project/admin/attendance.php">Manage Attendance</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-clock card-icon"></i>
                                <h5>Overtime Attendance</h5>
                                <a href="attendance/addatt.php">Update Overtime Attendance</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Reports -->
                <div id="section4" class="content-section">
                    <div class="section-title">Reports</div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-calendar-week card-icon"></i>
                                <h5>Attendance Report</h5>
                                <a href="reports/attdrep.php">View Attendance Report</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-cash-stack card-icon"></i>
                                <h5>Salary Report</h5>
                                <a href="reports/salrep.php">View Salary Report</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-credit-card card-icon"></i>
                                <h5>Payment Report</h5>
                                <a href="reports/pyntrep.php">View Payment Report</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-receipt card-icon"></i>
                                <h5>Expenses Report</h5>
                                <a href="reports/exprep.php">View Expense Report</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-clock-history card-icon"></i>
                                <h5>Overtime Attendance Report</h5>
                                <a href="reports/ovattrep.php">View Overtime Attendance Report</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-currency-exchange card-icon"></i>
                                <h5>Overtime Salary Report</h5>
                                <a href="reports/ovsalrep.php">View Overtime Salary Report</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 5: Work Orders -->
                <div id="section5" class="content-section">
                    <div class="section-title">Work Orders</div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-clipboard-check card-icon"></i>
                                <h5>Work Orders</h5>
                                <a href="reports/wrkrep.php">View Work Orders</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-hourglass-split card-icon"></i>
                                <h5>Ongoing Works</h5>
                                <a href="reports/onwrkrep.php">Ongoing works</a>
                            </div>
                        </div>
                        

                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-check-circle card-icon"></i>
                                <h5>Completed works</h5>
                                <a href="reports/cmpwrk.php">Completed Works</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 6: Expenses -->
                <div id="section6" class="content-section">
                    <div class="section-title">Expenses</div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-credit-card-2-front card-icon"></i>
                                <h5>Payments</h5>
                                <a href="paymnt.php">Add Payments</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-calculator card-icon"></i>
                                <h5>Expenses</h5>
                                <a href="expent.php">Add Expenses</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 7: Messages -->
                <div id="section7" class="content-section">
                    <div class="section-title">Messages</div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card dashboard-card text-center">
                                <i class="bi bi-chat-dots card-icon"></i>
                                <h5>Messages</h5>
                                <a href="messages.php">View Messages</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            var sections = document.querySelectorAll('.content-section');
            sections.forEach(function(section) {
                section.classList.remove('active');
            });
            document.getElementById(sectionId).classList.add('active');
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>