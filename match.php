<?php
require 'db.php';

$role    = $_GET['role'] ?? 'rider';
$section = $_GET['section'] ?? null;

if (!$section) {
    die("Section is required.");
}

$zoneMap = ["A" => 1, "B" => 2, "C" => 3, "D" => 4];
if (!isset($zoneMap[$section])) {
    die("Invalid section.");
}
$zoneID = $zoneMap[$section];

if ($role === "rider") {
    $title = "Providers available in Section $section";

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
} else {
    $title = "Riders available in Section $section";

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
}

$stmt->bind_param("i", $zoneID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Match Results</title>

    <link rel="stylesheet" href="index.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<header>
    <h1>Match Results</h1>
    <nav>
        <a href="index.html">Home</a>
        <a href="rider.html">Register Rider</a>
        <a href="provider.html">Register Provider</a>
        <a href="match.html">New Search</a>
    </nav>
</header>

<main class="container my-4">

    <section>
        <h2 class="mb-3"><?= htmlspecialchars($title) ?></h2>

        <?php if ($result->num_rows === 0): ?>

            <div class="alert alert-warning">
                No matches found in this section.
            </div>

        <?php else: ?>

            <div class="card p-3">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Address</th>

                            <?php if ($role === "rider"): ?>
                                <th>Car Plate</th>
                                <th>Car Model</th>
                            <?php endif; ?>

                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                                $address = htmlspecialchars($row['StreetName'] . " " . 
                                                            $row['StreetNumber'] . ", " .
                                                            $row['PostalCode']);
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($row['StudentName']) ?></td>
                                <td><?= $address ?></td>

                                <?php if ($role === "rider"): ?>
                                    <td><?= htmlspecialchars($row['CarPlateID'] ?? "N/A") ?></td>
                                    <td><?= htmlspecialchars($row['CarModel'] ?? "N/A") ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>

                </table>
            </div>

        <?php endif; ?>

    </section>

</main>

<footer>
    <small>CPSC 2221 – Carpooling Project</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
