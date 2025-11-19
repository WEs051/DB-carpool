<?php
require 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

// Personal info
$name      = $_POST['name'] ?? null;
$studentId = $_POST['studentId'] ?? null;
$email     = $_POST['email'] ?? null;
$phone     = $_POST['phone'] ?? null;

// Address
$street       = $_POST['street'] ?? null;
$streetNumber = $_POST['streetNumber'] ?? null;
$postalCode   = $_POST['postalCode'] ?? null;
$section      = $_POST['section'] ?? null;

// Vehicle
$plate    = $_POST['plate'] ?? null;
$make     = $_POST['make'] ?? null;
$model    = $_POST['model'] ?? null;
$capacity = $_POST['capacity'] ?? null; // NOTE: not stored in DB under Option A

// Basic validation
if (!$name || !$studentId || !$street || !$streetNumber || !$postalCode || !$section || !$plate) {
    die("Missing required fields. Please go back and fill in all required fields.");
}

// Map section A/B/C/D → Zone_ID 1/2/3/4
$zoneMap = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4];
if (!isset($zoneMap[$section])) {
    die("Invalid section.");
}
$zoneID = $zoneMap[$section];

try {
    $conn->begin_transaction();

    // 1) Insert into Address
    $stmt = $conn->prepare("
        INSERT INTO Address (StreetName, StreetNumber, PostalCode, Zone_ID)
        VALUES (?, ?, ?, ?)
    ");
    $streetNumberInt = (int)$streetNumber;
    $stmt->bind_param("sisi", $street, $streetNumberInt, $postalCode, $zoneID);

    if (!$stmt->execute()) {
        throw new Exception("Address insert failed: " . $stmt->error);
    }
    $addressId = $stmt->insert_id;
    $stmt->close();

    // 2) Insert into StudentUser
    $gender = null; // your form doesn't collect this
    $height = null; // your form doesn't collect this

    $stmt = $conn->prepare("
        INSERT INTO StudentUser
            (StudentID, StudentName, Gender, AddressID, StreetName, StreetNumber, PostalCode, Zone_ID, Height)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // Types: i = int, s = string, d = double
    // StudentID (i), StudentName (s), Gender (s),
    // AddressID (i), StreetName (s), StreetNumber (i),
    // PostalCode (s), Zone_ID (i), Height (d)

    $stmt->bind_param(
    "issisisid",
    $studentId,
    $name,
    $gender,
    $addressId,
    $street,
    $streetNumberInt,
    $postalCode,
    $zoneID,
    $height
    );

    if (!$stmt->execute()) {
        throw new Exception("StudentUser insert failed: " . $stmt->error);
    }
    $stmt->close();

    // 3) Insert into Providers (subtype)
    $stmt = $conn->prepare("INSERT INTO Providers (StudentID) VALUES (?)");
    $stmt->bind_param("i", $studentId);

    if (!$stmt->execute()) {
        throw new Exception("Providers insert failed: " . $stmt->error);
    }
    $stmt->close();

    // 4) Insert into Vehicle (CarPlateID, CarModel, OwnerStudentID)
    $carModel = trim($make . ' ' . $model); // combine make + model
    $stmt = $conn->prepare("
        INSERT INTO Vehicle (CarPlateID, CarModel, OwnerStudentID)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("ssi", $plate, $carModel, $studentId);

    if (!$stmt->execute()) {
        throw new Exception("Vehicle insert failed: " . $stmt->error);
    }
    $stmt->close();

    // Commit all
    $conn->commit();

    echo "<h2>Provider registered successfully!</h2>";
    echo '<p><a href="provider.html">Register another provider</a></p>';
    echo '<p><a href="index.html">Back to home</a></p>';

} catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
}

$conn->close();
?>




