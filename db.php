<?php
$host = "localhost";
$user = "root"; 
$pass = "Senpara@8";               // EMPTY password for XAMPP
$dbname = "DB_carpool";   // exact name of the database you created

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
