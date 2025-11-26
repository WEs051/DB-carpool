<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name       = $_POST['name'];
    $studentId  = $_POST['studentId'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];

    $street     = $_POST['street'];
    $city       = $_POST['city'];  
    $postalCode = $_POST['postalCode'];
    $section    = $_POST['section'];  

    $days       = $_POST['days'];
    $arrival    = $_POST['arrivalTime'];
    $depart     = $_POST['departureTime'];

    $zoneMap = ['A'=>1, 'B'=>2, 'C'=>3, 'D'=>4];
    $zoneID = $zoneMap[$section];

    $stmt = $conn->prepare("
        INSERT INTO Address (StreetName, StreetNumber, PostalCode, Zone_ID)
        VALUES (?, 0, ?, ?)
    ");
    $stmt->bind_param("ssi", $street, $postalCode, $zoneID);

    if(!$stmt->execute()) die("Address insert failed: " . $stmt->error);

    $addressID = $stmt->insert_id;
    $stmt->close();
    
    $stmt = $conn->prepare("
        INSERT INTO StudentUser
        (StudentID, StudentName, Gender, AddressID, StreetName, StreetNumber, PostalCode, Zone_ID, Height)
        VALUES (?, ?, '', ?, ?, 0, ?, ?, NULL)
    ");
    $stmt->bind_param("isissi", $studentId, $name, $addressID, $street, $postalCode, $zoneID);

    if(!$stmt->execute()) die("StudentUser insert failed: " . $stmt->error);
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO Riders (StudentID) VALUES (?)");
    $stmt->bind_param("i", $studentId);

    if(!$stmt->execute()) die("Riders insert failed: " . $stmt->error);

    echo "<h2>Rider registered successfully!</h2>";
    echo '<p><a href="rider.html">Register another rider</a></p>';
    echo '<p><a href="index.html">Back to home</a></p>';

    $stmt->close();
    $conn->close();
}
?>
