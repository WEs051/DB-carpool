<?php
require 'db.php';

if (!isset($conn)) {
    die("Database connection (\$conn) is not set. Please check db.php.");
}

$sql = "
    SELECT 
        O.StudentID,
        SUO.StudentName,
        SUO.Zone_ID
    FROM Riders O
    JOIN StudentUser SUO ON SUO.StudentID = O.StudentID
    WHERE NOT EXISTS (
        SELECT 1
        FROM Riders R
        JOIN StudentUser SUR ON SUR.StudentID = R.StudentID
        WHERE SUR.Zone_ID = SUO.Zone_ID
          AND R.StudentID <> O.StudentID       -- exclude themselves
          AND NOT EXISTS (                     -- this R must be covered
                SELECT 1
                FROM IsRidingWith IR
                WHERE IR.OrganizesRide_SID = O.StudentID
                AND IR.ComesWith_SID = R.StudentID
          )
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
    <title>Division Query – Riders Organizing All Rides</title>

    <link rel="stylesheet" href="index.css">

    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" 
        rel="stylesheet"
    >
</head>

<body>

<header>
    <h1>Division Query Result</h1>
    <nav>
        <a href="index.html">Home</a>
        <a href="rider.html">Register as Rider</a>
        <a href="provider.html">Register as Provider</a>
        <a href="match.html">Find Matches</a>
    </nav>
</header>

<main class="container my-4">

    <section>
        <h2 class="mb-3">Riders Who Organize Rides for ALL Riders in Their Zone</h2>

        <p class="mb-3">
            This page demonstrates a <strong>Division</strong> query.
            We return riders who organize rides for <em>every other rider</em> 
            in the same zone. Missing even one rider removes them.
        </p>

        <?php if ($result->num_rows === 0): ?>
            
            <div class="alert alert-warning">
                <em>No riders currently satisfy this division condition.</em>
            </div>

        <?php else: ?>

            <div class="card p-3">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Zone ID</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['StudentID']); ?></td>
                                <td><?php echo htmlspecialchars($row['StudentName']); ?></td>
                                <td><?php echo htmlspecialchars($row['Zone_ID']); ?></td>
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
$result->free();
$conn->close();
?>
