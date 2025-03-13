<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cbcnews";


// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo "Connected successfully";

error_reporting(0); // E_ALL

?>
