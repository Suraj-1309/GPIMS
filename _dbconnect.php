<!-- connect to database  -->
<?php

$insert = false;
$update = false;
$delete = false;


$servername = "localhost";
$username = "root";
$password = "";
$database = "admin";

$conn = mysqli_connect($servername, $username, $password, $database);

?>