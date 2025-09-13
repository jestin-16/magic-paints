<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$con = mysqli_connect($servername, $username, $password);
if (!$con) {
    die("Connection error");
}
mysqli_select_db($con, "projectm");

// Handle AJAX View Request
if (isset($_POST['action']) && $_POST['action'] == 'view') {
    $id = $_POST['id'];
    $query = "SELECT * FROM `bookingdetails` WHERE id='$id'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Display the details
        echo "<p><strong>Work ID:</strong> " . $row['id'] . "</p>";
        echo "<p><strong>Name:</strong> " . $row['Name'] . "</p>";
        echo "<p><strong>Phone:</strong> " . $row['phoneNo'] . "</p>";
        echo "<p><strong>Address:</strong> " . $row['address'] . "</p>";
        echo "<p><strong>Building Type:</strong> " . $row['build_type'] . "</p>";
        echo "<p><strong>Work Type:</strong> " . $row['work_type'] . "</p>";
        echo "<p><strong>Square Feet:</strong> " . $row['sqfeet'] . "</p>";
        echo "<p><strong>Status:</strong> " . $row['status'] . "</p>";
        echo "<p><strong>Time:</strong> " . $row['created_at'] . "</p>";
    } else {
        echo "<p>No details found for this Work Order.</p>";
    }
    exit();
}

// Add new AJAX handler for report generation
if (isset($_POST['action']) && $_POST['action'] == 'report') {
    $site_id = $_POST['id'];
    
    // Get site details first
    $site_query = "SELECT * FROM bookingdetails WHERE id='$site_id'";
    $site_result = mysqli_query($con, $site_query);
    $site_details = mysqli_fetch_assoc($site_result);

    // Get attendance data
    $attendance_query = "SELECT a.Emp_Id, a._date, a.Worktype, a.Session1, a.Session2, 
                               d.dailysalary, o.overtime_salary, ot.Workhours
                        FROM bookingdetails b
                        LEFT JOIN attendance a ON a._site = b.id
                        LEFT JOIN dsalary d ON d.Emp_Id = a.Emp_Id AND d._date = a._date
                        LEFT JOIN osalary o ON o.Emp_Id = a.Emp_Id AND o._date = a._date
                        LEFT JOIN overtimeattendance ot ON ot.Emp_Id = a.Emp_Id AND ot._date = a._date
                        WHERE b.id = '$site_id'";
    
    // Get expenses data
    $expenses_query = "SELECT e.t_amnt, e.r_amnt, e._date, e._tot 
                      FROM expenses e 
                      WHERE e.site_id = '$site_id'";
    
    // Get payments data
    $payments_query = "SELECT p.date, p.amount, p.total 
                      FROM payments p 
                      WHERE p.site_id = '$site_id'";

    $attendance_result = mysqli_query($con, $attendance_query);
    $expenses_result = mysqli_query($con, $expenses_query);
    $payments_result = mysqli_query($con, $payments_query);

    // Generate HTML report with enhanced styling
    $report = "<div class='report-container'>";
    
    // Header Section
    $report .= "<div class='report-header'>";
    $report .= "<h2 class='company-name'>PROJECTM CONSTRUCTION</h2>";
    $report .= "<h3 class='report-title'>Site Progress Report</h3>";
    $report .= "<div class='report-meta'>";
    $report .= "<p><strong>Report Date:</strong> " . date('d-m-Y') . "</p>";
    $report .= "<p><strong>Site ID:</strong> " . $site_id . "</p>";
    $report .= "</div>";
    
    // Client Details Section - Updated with better alignment
    $report .= "<div class='client-details'>";
    $report .= "<h4>Client Details</h4>";
    $report .= "<div class='client-info-grid'>";
    $report .= "<div class='info-row'>";
    $report .= "<div class='info-group'><label>Client Name:</label><span>" . $site_details['Name'] . "</span></div>";
    $report .= "<div class='info-group'><label>Contact:</label><span>" . $site_details['phoneNo'] . "</span></div>";
    $report .= "</div>";
    $report .= "<div class='info-row'>";
    $report .= "<div class='info-group'><label>Address:</label><span>" . $site_details['address'] . "</span></div>";
    $report .= "<div class='info-group'><label>Building Type:</label><span>" . $site_details['build_type'] . "</span></div>";
    $report .= "</div>";
    $report .= "<div class='info-row'>";
    $report .= "<div class='info-group'><label>Work Type:</label><span>" . $site_details['work_type'] . "</span></div>";
    $report .= "<div class='info-group'><label>Area:</label><span>" . $site_details['sqfeet'] . " sq.ft</span></div>";
    $report .= "</div>";
    $report .= "</div></div>";

    // Labor Details Section with summary
    $report .= "<div class='report-section'>";
    $report .= "<h4>Labor Summary</h4>";
    $report .= "<table class='table table-bordered table-sm report-table'>";
    $report .= "<thead><tr><th>Date</th><th>Employee ID</th><th>Work Type</th><th>Daily Salary</th><th>Overtime Hours</th><th>Overtime Salary</th></tr></thead><tbody>";
    
    while ($row = mysqli_fetch_assoc($attendance_result)) {
        $report .= "<tr>";
        $report .= "<td>" . $row['_date'] . "</td>";
        $report .= "<td>" . $row['Emp_Id'] . "</td>";
        $report .= "<td>" . $row['Worktype'] . "</td>";
        $report .= "<td>" . ($row['dailysalary'] ?? '0') . "</td>";
        $report .= "<td>" . ($row['Workhours'] ?? '0') . "</td>";
        $report .= "<td>" . ($row['overtime_salary'] ?? '0') . "</td>";
        $report .= "</tr>";
    }
    $report .= "</tbody></table>";

    // Expenses Section with summary
    $report .= "<div class='report-section'>";
    $report .= "<h4>Expenses Summary</h4>";
    $report .= "<table class='table table-bordered table-sm report-table'>";
    $report .= "<thead><tr><th>Date</th><th>Transport Amount</th><th>Raw Material Amount</th><th>Total</th></tr></thead><tbody>";
    
    while ($row = mysqli_fetch_assoc($expenses_result)) {
        $report .= "<tr>";
        $report .= "<td>" . $row['_date'] . "</td>";
        $report .= "<td>" . $row['t_amnt'] . "</td>";
        $report .= "<td>" . $row['r_amnt'] . "</td>";
        $report .= "<td>" . $row['_tot'] . "</td>";
        $report .= "</tr>";
    }
    $report .= "</tbody></table>";

    // Payments Section with summary
    $report .= "<div class='report-section'>";
    $report .= "<h4>Payments Summary</h4>";
    $report .= "<table class='table table-bordered table-sm report-table'>";
    $report .= "<thead><tr><th>Date</th><th>Amount</th><th>Total</th></tr></thead><tbody>";
    
    while ($row = mysqli_fetch_assoc($payments_result)) {
        $report .= "<tr>";
        $report .= "<td>" . $row['date'] . "</td>";
        $report .= "<td>" . $row['amount'] . "</td>";
        $report .= "<td>" . $row['total'] . "</td>";
        $report .= "</tr>";
    }
    $report .= "</tbody></table>";

    $report .= "</div>"; // Close report-container
    
    echo $report;
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Orders Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f8fa;
        }

        .container-title {
            text-align: center;
            padding: 20px;
            background-color: #343a40;
            color: white;
        }

        .table-container {
            max-width: 1200px;
            margin: 20px auto;
        }

        .table {
            background-color: white;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .modal-header {
            background-color: #007bff;
            color: white;
        }

        .search-box {
            margin-bottom: 15px;
        }

        .action-btns {
            display: flex;
            gap: 10px;
        }
        .action-btns .btn {
            white-space: nowrap;
            padding: 5px 10px;
        }

        /* Updated Report Styles */
        .report-container {
            padding: 30px;
            background: white;
            max-width: 1000px;
            margin: 0 auto;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 8px;
        }

        .report-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 25px;
            border-bottom: 3px solid #007bff;
            position: relative;
        }

        .report-header::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 30%;
            right: 30%;
            height: 1px;
            background: #007bff;
        }

        .company-name {
            font-size: 32px;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-title {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .report-meta {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            font-size: 15px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
        }

        .client-details {
            margin-bottom: 35px;
            padding: 25px;
            background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .client-details h4 {
            margin-bottom: 25px;
            color: #2c3e50;
            border-bottom: 2px solid #007bff;
            padding-bottom: 12px;
            font-weight: 600;
        }

        .client-info-grid {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .info-group {
            flex: 1;
            display: flex;
            align-items: baseline;
        }

        .info-group label {
            min-width: 130px;
            font-weight: 600;
            color: #34495e;
        }

        .info-group span {
            color: #2c3e50;
            font-size: 15px;
        }

        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
            }
        }

        .meta-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .meta-table td {
            padding: 5px 10px;
        }

        .meta-table td:first-child {
            width: 150px;
            font-weight: 500;
        }

        .report-section {
            margin-bottom: 35px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .report-section h4 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
        }

        .report-table {
            margin-bottom: 25px;
            font-size: 14px;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
        }

        .report-table th {
            background-color: #007bff !important;
            color: white !important;
            font-weight: 600;
            padding: 12px;
            text-align: left;
            border: none;
        }

        .report-table td {
            padding: 12px;
            border: 1px solid #e9ecef;
            background: white;
        }

        .report-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Print-specific styles */
        @media print {
            .report-container {
                box-shadow: none;
                padding: 15px;
            }
            
            .report-table th {
                background-color: #007bff !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .client-details {
                box-shadow: none;
                border: 1px solid #e9ecef;
            }
            
            .report-section {
                box-shadow: none;
                border: 1px solid #e9ecef;
            }
        }
    </style>
</head>
<body>
    <div class="container-title">
        <h3>Work Orders Report</h3>
    </div>
    <div class="container">
        <div class="d-flex align-items-center mb-3">
            <a href="http://localhost/project/admin/home.php" class="btn btn-primary mr-3">Home</a>
            <input type="text" class="form-control search-box" id="searchInput" placeholder="Search Work Orders...">
        </div>
    </div>
    <div class="container table-container">
        <?php
        $sql = "SELECT id, Name, phoneNo, address, build_type, work_type, sqfeet, status FROM `bookingdetails` WHERE `status`='Completed'";
        $res = mysqli_query($con, $sql);

        if (mysqli_num_rows($res) > 0) {
            echo "<div class='table-responsive'>";
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead><tr>";
            echo "<th>Work ID</th><th>Name</th><th>Phone Number</th><th>Address</th>";
            echo "<th>Building Type</th><th>Work Type</th><th>Square Feet</th><th>Status</th><th>Actions</th>";
            echo "</tr></thead><tbody id='workOrdersTable'>";
            
            while ($x = mysqli_fetch_array($res)) {
                echo "<tr data-id='".$x['id']."'>";
                echo "<td>".$x['id']."</td>";
                echo "<td>".$x['Name']."</td>";
                echo "<td>".$x['phoneNo']."</td>";
                echo "<td>".$x['address']."</td>";
                echo "<td>".$x['build_type']."</td>";
                echo "<td>".$x['work_type']."</td>";
                echo "<td>".$x['sqfeet']."</td>";
                echo "<td>".$x['status']."</td>";
                echo "<td class='action-btns'>";
                echo "<button class='btn btn-sm btn-info view-btn'>View</button>";
                echo "<button class='btn btn-sm btn-success report-btn'>Report</button>";
                echo "</td></tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "<div class='alert alert-warning'>No records found.</div>";
        }
        ?>
    </div>

    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Work Order Details</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="viewDetails"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Site Report</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="reportDetails"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="printReport()">Print</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Search functionality
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $("#workOrdersTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // View button
            $('.view-btn').click(function () {
                var id = $(this).closest('tr').data('id');
                $.post('', { action: 'view', id: id }, function (data) {
                    $('#viewDetails').html(data);
                    $('#viewModal').modal('show');
                });
            });

            // Report button
            $('.report-btn').click(function () {
                var id = $(this).closest('tr').data('id');
                $.post('', { action: 'report', id: id }, function (data) {
                    $('#reportDetails').html(data);
                    $('#reportModal').modal('show');
                });
            });
        });

        function printReport() {
            var printContents = document.getElementById('reportDetails').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
</body>
</html>
