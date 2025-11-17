<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Read form fields
    $name       = $_POST['name'];
    $studentId  = $_POST['studentId'];
    $email      = $_POST['email'];
    $phone      = $_POST['phone'];

    $street     = $_POST['street'];
    $city       = $_POST['city'];  // you may ignore for DB
    $postalCode = $_POST['postalCode'];
    $section    = $_POST['section'];   // A,B,C,D

    $days       = $_POST['days'];
    $arrival    = $_POST['arrivalTime'];
    $depart     = $_POST['departureTime'];

    // Convert section A/B/C/D → 1/2/3/4 (ZoneID)
    $zoneMap = ['A'=>1, 'B'=>2, 'C'=>3, 'D'=>4];
    $zoneID = $zoneMap[$section];

    // 1️⃣ Insert address
    $stmt = $conn->prepare("
        INSERT INTO Address (StreetName, StreetNumber, PostalCode, Zone_ID)
        VALUES (?, 0, ?, ?)
    ");
    $stmt->bind_param("ssi", $street, $postalCode, $zoneID);

    if(!$stmt->execute()) die("Address insert failed: " . $stmt->error);

    $addressID = $stmt->insert_id;
    $stmt->close();


    // 2️⃣ Insert into StudentUser
    $stmt = $conn->prepare("
        INSERT INTO StudentUser
        (StudentID, StudentName, Gender, AddressID, StreetName, StreetNumber, PostalCode, Zone_ID, Height)
        VALUES (?, ?, '', ?, ?, 0, ?, ?, NULL)
    ");
    $stmt->bind_param("isissi", $studentId, $name, $addressID, $street, $postalCode, $zoneID);

    if(!$stmt->execute()) die("StudentUser insert failed: " . $stmt->error);
    $stmt->close();


    // 3️⃣ Insert into Riders (subtype)
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
