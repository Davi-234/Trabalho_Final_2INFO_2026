<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "dev_share";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>