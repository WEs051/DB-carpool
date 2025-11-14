<?php
// match.php
require 'db.php';

$role    = $_GET['role'] ?? 'rider';
$section = $_GET['section'] ?? null;
$time    = $_GET['time'] ?? null;

if (!$section) {
    die("Section is required. Please go back and select a section.");
}

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "    <meta charset='UTF-8'>";
echo "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "    <title>Match Results</title>";
echo "    <link rel='stylesheet' href='index.css'>";
echo "</head>";
echo "<body>";
echo "<header>";
echo "    <h1>Carpool Match Results</h1>";
echo "    <nav>";
echo "        <a href='index.html'>Home</a>";
echo "        <a href='rider.html'>Register as Rider</a>";
echo "        <a href='provider.html'>Register as Provider</a>";
echo "        <a href='match.html'>New Search</a>";
echo "    </nav>";
echo "</header>";
echo "<main>";
echo "<section>";

if ($role === 'rider') {
    echo "<h2>Available Providers in Section " . htmlspecialchars($section) . "</h2>";

    if ($time) {
        // Simple time window of ±1 hour
        $stmt = $conn->prepare("
            SELECT name, email, phone, street, city, plate, make, model, capacity, days, arrivalTime, departureTime
            FROM Providers
            WHERE section = ?
              AND ABS(TIMESTAMPDIFF(MINUTE, arrivalTime, ?)) <= 60
        ");
        $stmt->bind_param("ss", $section, $time);
    } else {
        $stmt = $conn->prepare("
            SELECT name, email, phone, street, city, plate, make, model, capacity, days, arrivalTime, departureTime
            FROM Providers
            WHERE section = ?
        ");
        $stmt->bind_param("s", $section);
    }

    if (!$stmt->execute()) {
        die("Query failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p>No providers found for this section/time.</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Car</th>
                <th>Seats</th>
                <th>Days</th>
                <th>Arrival</th>
                <th>Departure</th>
              </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
            echo "<td>" . htmlspecialchars($row['street'] . ", " . $row['city']) . "</td>";
            echo "<td>" . htmlspecialchars($row['make'] . " " . $row['model'] . " (" . $row['plate'] . ")") . "</td>";
            echo "<td>" . htmlspecialchars($row['capacity']) . "</td>";
            echo "<td>" . htmlspecialchars($row['days']) . "</td>";
            echo "<td>" . htmlspecialchars($row['arrivalTime']) . "</td>";
            echo "<td>" . htmlspecialchars($row['departureTime']) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    }

    $stmt->close();

} else { // role = provider
    echo "<h2>Available Riders in Section " . htmlspecialchars($section) . "</h2>";

    if ($time) {
        $stmt = $conn->prepare("
            SELECT name, email, phone, street, city, days, arrivalTime, departureTime
            FROM Riders
            WHERE section = ?
              AND ABS(TIMESTAMPDIFF(MINUTE, arrivalTime, ?)) <= 60
        ");
        $stmt->bind_param("ss", $section, $time);
    } else {
        $stmt = $conn->prepare("
            SELECT name, email, phone, street, city, days, arrivalTime, departureTime
            FROM Riders
            WHERE section = ?
        ");
        $stmt->bind_param("s", $section);
    }

    if (!$stmt->execute()) {
        die("Query failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<p>No riders found for this section/time.</p>";
    } else {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Days</th>
                <th>Arrival</th>
                <th>Departure</th>
              </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
            echo "<td>" . htmlspecialchars($row['street'] . ", " . $row['city']) . "</td>";
            echo "<td>" . htmlspecialchars($row['days']) . "</td>";
            echo "<td>" . htmlspecialchars($row['arrivalTime']) . "</td>";
            echo "<td>" . htmlspecialchars($row['departureTime']) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    }

    $stmt->close();
}

$conn->close();

echo "</section>";
echo "</main>";
echo "<footer><small>CPSC 2221 – Carpooling Project</small></footer>";
echo "</body>";
echo "</html>";
?>
