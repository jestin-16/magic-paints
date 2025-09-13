<?php

$servername="localhost";
$username="root";
$password="";
$con=mysqli_connect($servername,$username,$password);
if(!$con)
{
  die("connection error");
}
$dbname="projectm";
mysqli_select_db($con,$dbname);

?>