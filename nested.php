<?php

require 'db.php';

if (!isset($conn)) {
    die("Database connection (\$conn) is not set. Please check db.php.");
}

$sql = "
    SELECT 
        Zone_ID,
        AVG(Height) AS AvgHeight
    FROM StudentUser
    GROUP BY Zone_ID
    HAVING AVG(Height) = (
        SELECT MAX(ZoneAvg)
        FROM (
            SELECT AVG(Height) AS ZoneAvg
            FROM StudentUser
            GROUP BY Zone_ID
        ) AS subquery
    );
";

$result = $conn->query($sql);
if (!$result) {
    die("Query error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Nested Aggregation Query</title>

    <link rel="stylesheet" href="index.css">

    <!-- Bootstrap CSS -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >
</head>

<body>

<header>
    <h1>Nested Aggregation Result</h1>
    <nav>
        <a href="index.html">Home</a>
        <a href="rider.html">Register as Rider</a>
        <a href="provider.html">Register as Provider</a>
        <a href="match.html">Find Matches</a>
    </nav>
</header>

<main class="container my-4">

    <section>
        <h2 class="mb-3">Zone With Highest Average Height</h2>

        <p class="mb-3">
            This query uses <strong>nested aggregation</strong> to compute the
            average height for each zone, then returns the zone(s) whose 
            average height equals the <em>maximum</em> average across all zones.
        </p>

        <?php if ($result->num_rows === 0): ?>
            <div class="alert alert-warning">No height data available.</div>
        <?php else: ?>

            <div class="card p-3">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Zone ID</th>
                            <th>Average Height</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['Zone_ID']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($row['AvgHeight'], 2)); ?></td>
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
$result->free();
$conn->close();
?>
