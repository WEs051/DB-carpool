<?php

require 'db.php'; 

if (!isset($conn)) {
    die("Database connection (\$conn) is not set. Check db.php.");
}


$sql_riders = "
    SELECT 
        SU.Zone_ID,
        A.ZoneName,
        COUNT(R.StudentID) AS RiderCount
    FROM Riders R
    JOIN StudentUser SU ON SU.StudentID = R.StudentID
    LEFT JOIN Area A ON A.ZoneID = SU.Zone_ID
    GROUP BY SU.Zone_ID, A.ZoneName
    ORDER BY SU.Zone_ID;
";

$result_riders = $conn->query($sql_riders);
if (!$result_riders) {
    die("Query error (riders): " . $conn->error);
}

$sql_providers = "
    SELECT 
        SU.Zone_ID,
        A.ZoneName,
        COUNT(P.StudentID) AS ProviderCount
    FROM Providers P
    JOIN StudentUser SU ON SU.StudentID = P.StudentID
    LEFT JOIN Area A ON A.ZoneID = SU.Zone_ID
    GROUP BY SU.Zone_ID, A.ZoneName
    ORDER BY SU.Zone_ID;
";

$result_providers = $conn->query($sql_providers);
if (!$result_providers) {
    die("Query error (providers): " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aggregation Queries</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<header>
    <h1>Aggregation Query Results</h1>
    <nav>
        <a href="index.html">Home</a>
        <a href="rider.html">Register as Rider</a>
        <a href="provider.html">Register as Provider</a>
        <a href="match.html">Find Matches</a>
    </nav>
</header>

<main>

    <section>
        <h2>Rider Count per Zone</h2>

        <?php if ($result_riders->num_rows === 0): ?>
            <p>No rider data found.</p>
        <?php else: ?>
            <table border="1" cellpadding="6" cellspacing="0">
                <thead>
                    <tr>
                        <th>Zone ID</th>
                        <th>Zone Name</th>
                        <th>Number of Riders</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_riders->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['Zone_ID']); ?></td>
                            <td><?php echo htmlspecialchars($row['ZoneName']); ?></td>
                            <td><?php echo htmlspecialchars($row['RiderCount']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <hr>

    <section>
        <h2>Provider Count per Zone</h2>

        <?php if ($result_providers->num_rows === 0): ?>
            <p>No provider data found.</p>
        <?php else: ?>
            <table border="1" cellpadding="6" cellspacing="0">
                <thead>
                    <tr>
                        <th>Zone ID</th>
                        <th>Zone Name</th>
                        <th>Number of Providers</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_providers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['Zone_ID']); ?></td>
                            <td><?php echo htmlspecialchars($row['ZoneName']); ?></td>
                            <td><?php echo htmlspecialchars($row['ProviderCount']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

</main>

<footer>
    <small>CPSC 2221 – Carpooling Project</small>
</footer>

</body>
</html>

<?php
$result_riders->free();
$result_providers->free();
$conn->close();
?>
