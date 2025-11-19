<?php
require 'db.php';

$role    = $_GET['role'] ?? 'rider';
$section = $_GET['section'] ?? null;

if (!$section) {
    die("Section is required.");
}

// Map Section → ZoneID used in Address table
$zoneMap = ["A" => 1, "B" => 2, "C" => 3, "D" => 4];
if (!isset($zoneMap[$section])) {
    die("Invalid section.");
}
$zoneID = $zoneMap[$section];

// HTML Header
echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<meta charset='UTF-8'>";
echo "<title>Match Results</title>";
echo "<link rel='stylesheet' href='index.css'>";
echo "</head><body>";

// Navbar
echo "
<header>
    <h1>Match Results</h1>
    <nav>
        <a href='index.html'>Home</a>
        <a href='rider.html'>Register Rider</a>
        <a href='provider.html'>Register Provider</a>
        <a href='match.html'>New Search</a>
    </nav>
</header>
";

echo "<main><section>";


// -------------------------------------------
// MATCH PROVIDERS (Rider searching providers)
// -------------------------------------------
if ($role === "rider") {

    echo "<h2>Providers available in Section $section</h2>";

    $stmt = $conn->prepare("
        SELECT 
            su.StudentName,
            a.StreetName,
            a.StreetNumber,
            a.PostalCode,
            v.CarPlateID,
            v.CarModel
        FROM Providers p
        JOIN StudentUser su ON su.StudentID = p.StudentID
        JOIN Address a ON a.AddressID = su.AddressID
        LEFT JOIN Vehicle v ON v.OwnerStudentID = p.StudentID
        WHERE su.Zone_ID = ?
    ");

    $stmt->bind_param("i", $zoneID);

}
// -------------------------------------------
// MATCH RIDERS (Provider searching riders)
// -------------------------------------------
else {

    echo "<h2>Riders available in Section $section</h2>";

    $stmt = $conn->prepare("
        SELECT 
            su.StudentName,
            a.StreetName,
            a.StreetNumber,
            a.PostalCode
        FROM Riders r
        JOIN StudentUser su ON su.StudentID = r.StudentID
        JOIN Address a ON a.AddressID = su.AddressID
        WHERE su.Zone_ID = ?
    ");

    $stmt->bind_param("i", $zoneID);

}



// -------------------------------------------
// EXECUTE QUERY
// -------------------------------------------
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No matches found.</p>";
} else {

    echo "<table border='1' cellpadding='5'>";
    echo "<tr>
            <th>Name</th>
            <th>Address</th>";

    if ($role === "rider") {
        echo "<th>Car Plate</th>";
        echo "<th>Car Model</th>";
    }

    echo "</tr>";

    while ($row = $result->fetch_assoc()) {

        $address = htmlspecialchars(
            $row['StreetName'] . " " . 
            $row['StreetNumber'] . ", " . 
            $row['PostalCode']
        );

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['StudentName']) . "</td>";
        echo "<td>$address</td>";

        if ($role === "rider") {
            echo "<td>" . htmlspecialchars($row['CarPlateID'] ?? "N/A") . "</td>";
            echo "<td>" . htmlspecialchars($row['CarModel'] ?? "N/A") . "</td>";
        }

        echo "</tr>";
    }

    echo "</table>";
}

echo "</section></main>";
echo "<footer><small>CPSC 2221 – Carpooling Project</small></footer>";
echo "</body></html>";

$stmt->close();
$conn->close();
?>
