<?php
$servername = "localhost";
$username = "devshare";
$password = "joao@MV1";
$dbname = "dev_share";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if ($conn === false) {
  throw new RuntimeException("Não foi possível conectar ao banco de dados.");
}
?>