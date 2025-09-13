<?php

$connection=require("conn.php");
$no=$_GET['no'];
$sql=" DELETE  FROM `customer` WHERE `cust_id`='$no'";
$res=mysqli_query($con,$sql);
if($res)
{
  header("location:display.php");
}
else
echo "error";
?>

