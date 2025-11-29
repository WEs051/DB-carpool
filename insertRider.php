<?php
require 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

// Personal info
$name       = $_POST['name'];
$studentId  = $_POST['studentId'];
$phone      = $_POST['phone'];

// Address info
$street     = $_POST['street'];
$city       = $_POST['city'];
$postalCode = $_POST['postalCode'];
$section    = $_POST['section'];

$days       = $_POST['days'];
$arrival    = $_POST['arrivalTime'];
$depart     = $_POST['departureTime'];

$zoneMap = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4];
$zoneID = $zoneMap[$section];

$stmt = $conn->prepare("
    INSERT INTO Address (StreetName, StreetNumber, PostalCode, Zone_ID)
    VALUES (?, 0, ?, ?)
");
$stmt->bind_param("ssi", $street, $postalCode, $zoneID);
$stmt->execute();
$addressID = $stmt->insert_id;
$stmt->close();

$stmt = $conn->prepare("
    INSERT INTO StudentUser
    (StudentID, StudentName, Gender, AddressID, StreetName, StreetNumber, PostalCode, Zone_ID, Height)
    VALUES (?, ?, '', ?, ?, 0, ?, ?, NULL)
");
$stmt->bind_param("isissi", $studentId, $name, $addressID, $street, $postalCode, $zoneID);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("INSERT INTO Riders (StudentID) VALUES (?)");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rider Registration Success</title>
    <link rel="stylesheet" href="index.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>

<header>
    <h1>Rider Registration</h1>
    <nav>
        <a href="index.html">Home</a>
        <a href="provider.html">Register Provider</a>
        <a href="match.html">Find Matches</a>
    </nav>
</header>

<main>
<div class="success-card shadow p-4">

    <h2 class="text-success">Rider registered successfully!</h2>

    <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
    <p><strong>Student ID:</strong> <?php echo htmlspecialchars($studentId); ?></p>
    <p><strong>Section:</strong> <?php echo htmlspecialchars($section); ?></p>

    <hr class="mt-4">

    <div class="d-grid gap-3 mt-4 mb-3">
        <a href="rider.html" class="btn btn-primary btn-lg">Register Another Rider</a>
        <a href="index.html" class="btn btn-secondary btn-lg">Back to Home</a>
    </div>

</div>
</main>

<footer>
    <small>CPSC 2221 – Carpooling Project</small>
</footer>

</body>
</html>
