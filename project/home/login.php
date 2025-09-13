<?php
session_start();

$host = "localhost";
$user = "root";
$password = ""; 
$db = "projectm";

$connection = require("conn.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT cust_name, Password, role FROM customer WHERE cust_name='$username'";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row && password_verify($password, $row['Password'])) {
        $_SESSION["username"] = $username;
        if ($row["role"] == "admin") {
            header("Location: http://localhost/project/admin/home.php");
            exit();
        } elseif ($row["role"] == "user") {
            header("Location: http://localhost/project/user/home.php");
            exit();
        } elseif ($row["role"] == "worker") {
            header("Location: http://localhost/project/worker/home.php");
            exit();
        } else {
            $error_message = "Invalid user type";
        }
    } else {
        $error_message = "Username or password is incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Poppins", sans-serif;
    }
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: url(imgs/color4.jpg) no-repeat;
      background-size: cover;
      background-position: center;
    }
    .wrapper {
      width: 420px;
      background: transparent;
      border: 2px solid rgba(255, 255, 255, .2);
      backdrop-filter: blur(9px);
      color: #fff;
      border-radius: 12px;
      padding: 30px 40px;
    }
    .wrapper h1 {
      font-size: 36px;
      text-align: center;
    }
    .input-box {
      position: relative;
      width: 100%;
      height: 50px;
      margin: 30px 0;
    }
    .input-box input {
      width: 100%;
      height: 100%;
      background: transparent;
      border: none;
      outline: none;
      border: 2px solid rgba(255, 255, 255, .2);
      border-radius: 40px;
      font-size: 16px;
      color: #fff;
      padding: 20px 45px 20px 20px;
    }
    .input-box input::placeholder {
      color: #fff;
    }
    .input-box i {
      position: absolute;
      right: 20px;
      top: 30%;
      transform: translate(-50%);
      font-size: 20px;
    }
    .remember-forgot {
      display: flex;
      justify-content: space-between;
      font-size: 14.5px;
      margin: -15px 0 15px;
    }
    .remember-forgot label input {
      accent-color: #fff;
      margin-right: 3px;
    }
    .remember-forgot a {
      color: #fff;
      text-decoration: none;
    }
    .remember-forgot a:hover {
      text-decoration: underline;
    }
    .btn {
      width: 100%;
      height: 45px;
      background: #fff;
      border: none;
      outline: none;
      border-radius: 40px;
      box-shadow: 0 0 10px rgba(0, 0, 0, .1);
      cursor: pointer;
      font-size: 16px;
      color: #333;
      font-weight: 600;
    }
    .register-link {
      font-size: 14.5px;
      text-align: center;
      margin: 20px 0 15px;
    }
    .register-link p a {
      color: #fff;
      text-decoration: none;
      font-weight: 600;
    }
    .register-link p a:hover {
      text-decoration: underline;
    }
    .error-message {
      color: #ff4d4d;
      background: rgba(255, 77, 77, 0.1);
      padding: 10px;
      border-radius: 5px;
      text-align: center;
      margin-bottom: 15px;
    }
  </style>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/js/bootstrap.min.js"></script>

<div class="wrapper">
    <form action="#" method="POST" class="login-from">
        <h1>Login</h1>
        <div class="input-box">
            <input type="text" placeholder="Username" required name="username">
            <i class='bx bxs-user'></i>
        </div>
        <div class="input-box">
            <input type="password" placeholder="Password" required name="password">
            <i class='bx bxs-lock-alt'></i>
        </div>

        <!-- Error Message Placeholder -->
        <?php if (isset($error_message)) { ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php } ?>

        <button type="submit" class="btn">Login</button>
        <div class="register-link">
            <p>Don't have an account? <a href="sign.html">Register</a></p>
        </div>
    </form>
</div>
</body>
</html>