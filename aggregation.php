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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Aggregation Queries</title>

    <link rel="stylesheet" href="index.css">

    <!-- Bootstrap CSS -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >
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

<main class="container my-4">

    <!-- Rider Aggregation -->
    <section>
        <h2 class="mb-3">Rider Count per Zone</h2>

        <?php if ($result_riders->num_rows === 0): ?>
            <div class="alert alert-warning">No rider data found.</div>
        <?php else: ?>
            <div class="card mb-4 p-3">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
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
            </div>
        <?php endif; ?>
    </section>

    <!-- Provider Aggregation -->
    <section>
        <h2 class="mb-3">Provider Count per Zone</h2>

        <?php if ($result_providers->num_rows === 0): ?>
            <div class="alert alert-warning">No provider data found.</div>
        <?php else: ?>
            <div class="card p-3">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
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
            </div>
        <?php endif; ?>
    </section>

</main>

<footer>
    <small>CPSC 2221 – Carpooling Project</small>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
$result_riders->free();
$result_providers->free();
$conn->close();
?>
