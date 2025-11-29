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
$capacity = $_POST['capacity'] ?? null; 

// Basic validation
if (!$name || !$studentId || !$street || !$streetNumber || !$postalCode || !$section || !$plate) {
    die("Missing required fields. Please go back and fill in all required fields.");
}

$zoneMap = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4];
if (!isset($zoneMap[$section])) {
    die("Invalid section.");
}
$zoneID = $zoneMap[$section];

try {
    $conn->begin_transaction();

    $stmt = $conn->prepare("
        INSERT INTO Address (StreetName, StreetNumber, PostalCode, Zone_ID)
        VALUES (?, ?, ?, ?)
    ");
    $streetNumberInt = (int)$streetNumber;
    $stmt->bind_param("sisi", $street, $streetNumberInt, $postalCode, $zoneID);

    if (!$stmt->execute()) throw new Exception("Address insert failed: " . $stmt->error);

    $addressId = $stmt->insert_id;
    $stmt->close();

    $gender = null;
    $height = null;

    $stmt = $conn->prepare("
        INSERT INTO StudentUser
            (StudentID, StudentName, Gender, AddressID, StreetName, StreetNumber, PostalCode, Zone_ID, Height)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issisisid",
        $studentId, $name, $gender,
        $addressId, $street, $streetNumberInt,
        $postalCode, $zoneID, $height
    );

    if (!$stmt->execute()) throw new Exception("StudentUser insert failed: " . $stmt->error);
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO Providers (StudentID) VALUES (?)");
    $stmt->bind_param("i", $studentId);

    if (!$stmt->execute()) throw new Exception("Providers insert failed: " . $stmt->error);
    $stmt->close();

    $carModel = trim($make . " " . $model);

    $stmt = $conn->prepare("
        INSERT INTO Vehicle (CarPlateID, CarModel, OwnerStudentID)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("ssi", $plate, $carModel, $studentId);

    if (!$stmt->execute()) throw new Exception("Vehicle insert failed: " . $stmt->error);
    $stmt->close();

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Provider Registered</title>

    <link rel="stylesheet" href="index.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<header>
    <h1>Provider Registration</h1>
    <nav>
        <a href="index.html">Home</a>
        <a href="rider.html">Register Rider</a>
        <a href="provider.html">Register Provider</a>
        <a href="match.html">Find Matches</a>
    </nav>
</header>

<main class="container mt-4">

    <div class="card p-4 shadow-sm">

        <h2 class="text-success mb-3">Provider registered successfully!</h2>

        <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
        <p><strong>Student ID:</strong> <?= htmlspecialchars($studentId) ?></p>
        <p><strong>Section:</strong> <?= htmlspecialchars($section) ?></p>

        <hr>

        <a href="provider.html" class="btn btn-primary w-100 mb-3">Register Another Provider</a>
        <a href="index.html" class="btn btn-secondary w-100">Back to Home</a>

    </div>

</main>

<footer>
    <small>CPSC 2221 – Carpooling Project</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
