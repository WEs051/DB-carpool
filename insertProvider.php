<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Personal info
    $name          = $_POST['name'] ?? null;
    $studentId     = $_POST['studentId'] ?? null;
    $email         = $_POST['email'] ?? null;
    $phone         = $_POST['phone'] ?? null;

    // Address
    $street        = $_POST['street'] ?? null;
    $city          = $_POST['city'] ?? null;
    $postalCode    = $_POST['postalCode'] ?? null;
    $section       = $_POST['section'] ?? null;

    // Vehicle
    $plate         = $_POST['plate'] ?? null;
    $make          = $_POST['make'] ?? null;
    $model         = $_POST['model'] ?? null;
    $capacity      = $_POST['capacity'] ?? null;

    // Schedule
    $days          = $_POST['days'] ?? null;
    $arrivalTime   = $_POST['arrivalTime'] ?? null;
    $departureTime = $_POST['departureTime'] ?? null;

    if (!$name || !$studentId || !$email || !$street || !$postalCode || !$section || !$plate || !$capacity) {
        die("Missing required fields. Please go back and fill in all required fields.");
    }

    $stmt = $conn->prepare("
        INSERT INTO Providers
            (name, studentId, email, phone, street, city, postalCode, section,
             plate, make, model, capacity, days, arrivalTime, departureTime)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "sssssssssssisss",
        $name,
        $studentId,
        $email,
        $phone,
        $street,
        $city,
        $postalCode,
        $section,
        $plate,
        $make,
        $model,
        $capacity,
        $days,
        $arrivalTime,
        $departureTime
    );

    if ($stmt->execute()) {
        echo "<p>Provider registered successfully!</p>";
        echo '<p><a href="provider.html">Register another provider</a> | <a href="index.html">Back to home</a></p>';
    } else {
        echo "Error inserting provider: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
