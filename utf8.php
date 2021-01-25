<?php
include 'connect.php';
$q="select name from players where id=1";
$res=mysqli_query($c,$q);
$row=mysqli_fetch_assoc($res);
echo $row['name'];
?>

