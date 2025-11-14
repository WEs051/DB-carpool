<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name          = $_POST['name'] ?? null;
    $studentId     = $_POST['studentId'] ?? null;
    $email         = $_POST['email'] ?? null;
    $phone         = $_POST['phone'] ?? null;

    $street        = $_POST['street'] ?? null;
    $city          = $_POST['city'] ?? null;
    $postalCode    = $_POST['postalCode'] ?? null;
    $section       = $_POST['section'] ?? null;

    $days          = $_POST['days'] ?? null;
    $arrivalTime   = $_POST['arrivalTime'] ?? null;
    $departureTime = $_POST['departureTime'] ?? null;

    if (!$name || !$studentId || !$email || !$street || !$postalCode || !$section) {
        die("Missing required fields. Please go back and fill in all required fields.");
    }

    $stmt = $conn->prepare("
        INSERT INTO Riders
            (name, studentId, email, phone, street, city, postalCode, section, days, arrivalTime, departureTime)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "sssssssssss",
        $name,
        $studentId,
        $email,
        $phone,
        $street,
        $city,
        $postalCode,
        $section,
        $days,
        $arrivalTime,
        $departureTime
    );

    if ($stmt->execute()) {
        echo "<p>Rider registered successfully!</p>";
        echo '<p><a href="rider.html">Register another rider</a> | <a href="index.html">Back to home</a></p>';
    } else {
        echo "Error inserting rider: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
