<?php
$customerName=$_POST['cname'];
$customerPhoneNumber=$_POST['cphone'];
$customerPassword=$_POST['cpass'];
$custid=1000;
$custid+=1;
$hashed=password_hash($customerPassword,PASSWORD_DEFAULT);
$connectin=require("conn.php");
$sql="INSERT INTO `customer`( `cust_name`, `cust_ph`, `Password`)
 VALUES ('$customerName','$customerPhoneNumber','$hashed')";
 $result=mysqli_query($con,$sql);
if($result)
{
  header("location:login.php");
}
else{
  echo "connection error";
}
?>