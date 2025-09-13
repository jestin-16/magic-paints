<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
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
        <h2>Attendance</h2>
        <div class="mb-3">
            <label for="mainDatePicker" class="form-label">Select Date:</label>
            <input type="date" id="mainDatePicker" class="form-control">
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Employee Name</th>
                    <th>Session 1</th>
                    <th>Session 2</th>
                </tr>
            </thead>
            <tbody id="attendanceBody">
                <tr>
                    <td><input type="date" class="date-picker"></td>
                    <td>Jestin</td>
                    <td><input type="checkbox" checked></td>
                    <td><input type="checkbox"></td>
                </tr>
                <tr>
                    <td><input type="date" class="date-picker"></td>
                    <td>Ajith</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox" checked></td>
                </tr>
                <tr>
                    <td><input type="date" class="date-picker"></td>
                    <td>Jestin</td>
                    <td><input type="checkbox"></td>
                    <td><input type="checkbox" checked></td>
                </tr>
                <tr>
                    <td><input type="date" class="date-picker"></td>
                    <td>Ajith</td>
                    <td><input type="checkbox" checked></td>
                    <td><input type="checkbox"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mainDatePicker = document.getElementById('mainDatePicker');
            const datePickers = document.querySelectorAll('.date-picker');

            const setAllDates = (date) => {
                datePickers.forEach(picker => picker.value = date);
            };

            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };

            const today = new Date();
            const todayFormatted = formatDate(today);

            mainDatePicker.value = todayFormatted;
            setAllDates(todayFormatted);

            mainDatePicker.addEventListener('change', (event) => {
                setAllDates(event.target.value);
            });
        });
    </script>
</body>
</html>
