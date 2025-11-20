<?php
$host = "localhost";
$user = "root";           // please use your own user name
$pass = "";               // please use your own and password for entering 
$dbname = "DB_carpool";   

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
