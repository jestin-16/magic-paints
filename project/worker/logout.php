<?php
session_start();
session_destroy();
header("Location: http://localhost/project/home/login.php");
exit();
?>
