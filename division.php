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
        SELECT R.StudentID
        FROM Riders R
        JOIN StudentUser SUR ON SUR.StudentID = R.StudentID
        WHERE 
            SUR.Zone_ID = SUO.Zone_ID      -- same zone
            AND R.StudentID <> O.StudentID -- other riders in that zone
            AND NOT EXISTS (               -- for each such rider, there must be a row in IsRidingWith
                SELECT 1
                FROM IsRidingWith IR
                WHERE 
                    IR.OrganizesRide_SID = O.StudentID
                    AND IR.ComesWith_SID = R.StudentID
            )
    )
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
    <title>Division Query – Riders Organizing All Rides in Zone</title>
    <link rel="stylesheet" href="index.css">
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

<main>
    <section>
        <h2>Riders who organize rides for ALL other riders in their zone</h2>
        <p>
            This demonstrates a <strong>division</strong> query: for each organizer rider, we check that there is 
            <em>no</em> rider in the same zone that they are not organizing a ride with.
        </p>

        <?php if ($result->num_rows === 0): ?>
            <p><em>No riders currently satisfy this condition.</em></p>
        <?php else: ?>
            <table border="1" cellpadding="6" cellspacing="0">
                <thead>
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
        <?php endif; ?>
    </section>
</main>

<footer>
    <small>CPSC 2221 – Carpooling Project</small>
</footer>
</body>
</html>
<?php
$result->free();
$conn->close();
?>
