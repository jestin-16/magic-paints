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
            margin: 0;
            padding: 0;
            background-color: #f7f8fa;
        }

        .container-title {
            text-align: center;
            padding: 20px 0;
            background-color: #343a40;
            color: white;
            margin-bottom: 20px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .button-container form,
        .button-container button,
        .button-container a {
            margin: 0 5px;
        }

        .button-container input[type="date"] {
            width: auto;
            padding: 10px;
            margin-right: 5px;
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

        th,
        td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .btn-view {
            padding: 8px 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-view:hover {
            background-color: #218838;
        }

        @media (max-width: 768px) {
            .button-container {
                flex-direction: column;
            }

            .button-container input[type="date"],
            .button-container input[type="submit"],
            .button-container button,
            .button-container a {
                width: 100%;
                margin-bottom: 10px;
            }

            th,
            td {
                font-size: 14px;
                padding: 10px;
            }

            .btn-view {
                padding: 6px 12px;
                font-size: 14px;
            }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body>
    <div class="container-title">
        <h3>Attendance Report</h3>
    </div>

    <div class="container">
        <div class="button-container">
            <button id="prevDay" class="btn btn-info">Previous Day</button>
            <form method="POST" action="" class="d-inline">
                <input type="date" id="mainDatePicker" class="form-control d-inline" name="attdate">
                <input type="submit" value="View" name="view" class="btn btn-primary mx-1">
            </form>
            <a href="http://localhost/project/admin/home.php" class="btn btn-primary">Home</a>
            <button type="button" onclick="generatePDF()" class="btn btn-success">Export to PDF</button>
            <button id="nextDay" class="btn btn-info">Next Day</button>
        </div>

        <?php
        $d = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        if (isset($_POST['view'])) {
            $d = $_POST['attdate'];
        }

        $sql = "SELECT `Emp_Id`, `_date`, `Worktype`, `Session1`, `Session2` FROM `attendance` WHERE `_date` = '$d'";
        $res = mysqli_query($con, $sql);

        if (!$res) {
            echo "<div class='container'>";
            echo "<p class='alert alert-danger mt-5'>Error executing query: " . mysqli_error($con) . "</p>";
            echo "</div>";
        } elseif (mysqli_num_rows($res) > 0) {
            echo "<div class='container'>";
            echo "<h4 class='mt-5 mb-3'>Attendance Report for " . date("F j, Y", strtotime($d)) . "</h4>";
            echo "<div class='table-responsive'>";
            echo "<table id='attendanceTable' class='table table-bordered table-striped'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Employee ID</th>";
            echo "<th>Date</th>";
            echo "<th>Work Type</th>";
            echo "<th>Session 1</th>";
            echo "<th>Session 2</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($x = mysqli_fetch_array($res)) {
                echo "<tr>";
                echo "<td>" . $x['Emp_Id'] . "</td>";
                echo "<td>" . date("F j, Y", strtotime($x['_date'])) . "</td>";
                echo "<td>" . $x['Worktype'] . "</td>";
                echo "<td>" . $x['Session1'] . "</td>";
                echo "<td>" . $x['Session2'] . "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<div class='container'>";
            echo "<p class='alert alert-warning mt-5'>No records found for the selected date.</p>";
            echo "</div>";
        }
        ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.22/jspdf.plugin.autotable.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const mainDatePicker = document.getElementById('mainDatePicker');
            mainDatePicker.value = '<?php echo $d; ?>';

            function updateAttendance(date) {
                const newUrl = window.location.pathname + '?date=' + date;
                window.history.replaceState({ date: date }, '', newUrl);
                mainDatePicker.value = date;
                window.location.reload();
            }

            document.getElementById('prevDay').addEventListener('click', function () {
                const prevDate = new Date(mainDatePicker.value);
                prevDate.setDate(prevDate.getDate() - 1);
                updateAttendance(prevDate.toISOString().split('T')[0]);
            });

            document.getElementById('nextDay').addEventListener('click', function () {
                const nextDate = new Date(mainDatePicker.value);
                nextDate.setDate(nextDate.getDate() + 1);
                updateAttendance(nextDate.toISOString().split('T')[0]);
            });
        });

        function generatePDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            doc.text('Attendance Report', 14, 16);

            const table = document.getElementById('attendanceTable');
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent || th.innerText);
            const rows = Array.from(table.querySelectorAll('tbody tr')).map(tr =>
                Array.from(tr.querySelectorAll('td')).map(td => td.textContent || td.innerText)
            );

            doc.autoTable({
                startY: 20,
                head: [headers],
                body: rows,
                theme: 'striped',
                margin: { left: 14, right: 14 },
                styles: {
                    fontSize: 10,
                    cellPadding: 2,
                    valign: 'middle',
                    halign: 'center'
                },
                headStyles: {
                    fillColor: [0, 123, 255],
                    textColor: [255, 255, 255]
                },
                columnStyles: {
                    0: { cellWidth: 30 },
                    1: { cellWidth: 40 },
                    2: { cellWidth: 40 },
                    3: { cellWidth: 30 },
                    4: { cellWidth: 30 }
                }
            });

            doc.save('attendance-report.pdf');
        }
    </script>
</body>

</html>
