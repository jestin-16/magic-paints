<?php 
session_start();

if (!isset($_SESSION["username"])) {
  header("Location: http://localhost/project/home/login.php");
  exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projectm";
$con = mysqli_connect($servername, $username, $password, $dbname);

if (!$con) {
  die("Connection error: " . mysqli_connect_error());
}

if (isset($_POST['reg'])) {
    $sessionUsername = $_SESSION["username"];
    $name = $_POST['Name'];
    $email = $_POST['email'];
    $phoneNo = $_POST['phoneNo'];
    $address = $_POST['address'];
    $loc = $_POST['location'];
    $buildType = $_POST['build-type'];
    $workType = $_POST['worktype'];
    $sqfeet = $_POST['sqfeet'];

    // Insert the data into the bookingdetails table
    $insql = "INSERT INTO `bookingdetails` (`username`, `Name`, `email`, `phoneNo`, `address`, `location`, `build_type`, `work_type`, `sqfeet`, `status`) 
              VALUES ('$sessionUsername', '$name', '$email', '$phoneNo', '$address', '$loc', '$buildType', '$workType', '$sqfeet', 'Pending')";

    $inres = mysqli_query($con, $insql);
    
    if ($inres) {
        echo "<script>
                alert('Booking successful! We will contact you shortly for more details.');
                window.location.href = 'http://localhost/project/user/home.php';
              </script>";
        exit();
    } else {
        die("Error: " . mysqli_error($con));
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Work</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: 100vh;
      margin: 0;
      padding: 40px 20px;
    }

    .container {
      background-color: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      max-width: 800px;
      margin: 0 auto;
      position: relative;
      backdrop-filter: blur(10px);
    }

    h2 {
      text-align: center;
      margin-bottom: 35px;
      font-weight: 700;
      color: #2c3e50;
      font-size: 2.2rem;
      position: relative;
      padding-bottom: 15px;
    }

    h2::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 4px;
      background: linear-gradient(90deg, #3498db, #2980b9);
      border-radius: 2px;
    }

    .form-label {
      font-weight: 500;
      color: #34495e;
      margin-bottom: 8px;
      font-size: 0.95rem;
    }

    .form-control, .form-select {
      border-radius: 8px;
      border: 2px solid #e0e6ed;
      padding: 12px 15px;
      margin-bottom: 20px;
      transition: all 0.3s ease;
      font-size: 0.95rem;
    }

    .form-control:focus, .form-select:focus {
      border-color: #3498db;
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    textarea.form-control {
      min-height: 120px;
    }

    .btn-primary {
      background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: 600;
      letter-spacing: 0.5px;
      width: 100%;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 15px rgba(52, 152, 219, 0.3);
    }

    .btn-secondary {
      background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: 600;
      letter-spacing: 0.5px;
      width: 100%;
      margin-top: 15px;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 15px rgba(127, 140, 141, 0.3);
    }

    .error {
      color: #e74c3c;
      font-size: 0.85rem;
      margin-top: -15px;
      margin-bottom: 15px;
      display: block;
    }

    .form-select {
      cursor: pointer;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23343a40' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 1rem center;
      background-size: 16px 12px;
    }

    @media (max-width: 768px) {
      .container {
        padding: 25px;
      }

      h2 {
        font-size: 1.8rem;
      }
    }

    /* Add subtle animation for form elements */
    .form-control, .form-select {
      transform: translateY(5px);
      opacity: 0;
      animation: fadeInUp 0.5s ease forwards;
    }

    @keyframes fadeInUp {
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    /* Add animation delay for each form element */
    .form-control:nth-child(1), .form-select:nth-child(1) { animation-delay: 0.1s; }
    .form-control:nth-child(2), .form-select:nth-child(2) { animation-delay: 0.2s; }
    .form-control:nth-child(3), .form-select:nth-child(3) { animation-delay: 0.3s; }
    /* ... and so on */
  </style>
</head>
<body>
  <div class="container">
    <h2>Book Work</h2>
    <form method="POST" action="#" onsubmit="return validate()">
      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" class="form-control" name="Name" id="_name" required>
        <span id="error1" class="error"></span>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" class="form-control" name="email" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text" class="form-control" name="phoneNo" id="ph" required>
        <span id="error2" class="error"></span>
      </div>
      <div class="mb-3">
        <label class="form-label">Address</label>
        <textarea class="form-control" name="address" rows="3" required></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" class="form-control" name="location" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Building Type</label>
        <select class="form-select" id="build-type" name="build-type">
          <option value="House">House</option>
          <option value="Apartment">Apartment</option>
          <option value="Office">Office</option>
          <option value="Shop">Shop</option>
          <option value="Warehouse">Warehouse</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Work Type</label>
        <select class="form-select" id="worktype" name="worktype" onchange="handleWorkTypeChange()">
          <option value="squarefeet">Square Feet</option>
          <option value="Tatch">Tatch</option>
        </select>
      </div>
      <div class="mb-3" id="sqfeet-container">
        <label class="form-label">Square Feet</label>
        <input type="text" class="form-control" name="sqfeet" id="sqfeet">
      </div>
      <div class="alert alert-info mb-3" role="alert">
        Note: We will contact you shortly for more details about your booking.
      </div>
      <div class="d-grid">
        <input type="submit" class="btn btn-primary" value="Book" name="reg">
        <input type="reset" class="btn btn-secondary" value="Reset">
      </div>
    </form>
  </div>

  <script>
    function validate() {
      let user = document.getElementById("_name");
      let Phno = document.getElementById("ph");
      let error1 = document.getElementById("error1");
      let error2 = document.getElementById("error2");

      let namePattern = /^[a-zA-Z\s]{5,}$/;
      let phonePattern = /^\d{10}$/;

      let valid = true;

      if (!namePattern.test(user.value)) {
        error1.innerHTML = "User name must be at least 5 characters long and contain only letters and spaces.";
        valid = false;
      } else {
        error1.innerHTML = "";
      }

      if (!phonePattern.test(Phno.value)) {
        error2.innerHTML = "Phone number must be 10 digits long.";
        valid = false;
      } else {
        error2.innerHTML = "";
      }

      return valid;
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
