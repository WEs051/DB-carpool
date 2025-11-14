<?php
$host = "localhost";
$user = "root"; 
$pass = "!uhj[rBq8s(bRh15";          
$dbname = "m3temp"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
