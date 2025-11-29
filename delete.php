<?php
require 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$studentID = $_POST['studentID'] ?? null;
$errors = [];

if (!$studentID) {
    $errors[] = "Student ID is required.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Delete Student Record</title>

    <link rel="stylesheet" href="index.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>

<header>
    <h1>Delete Student Record</h1>
    <nav>
        <a href="index.html">Home</a>
        <a href="rider.html">Register as Rider</a>
        <a href="provider.html">Register as Provider</a>
        <a href="match.html">Find Matches</a>
    </nav>
</header>

<main class="container my-4">

<section class="card-custom">

<?php
if (!empty($errors)) {
    echo "<h3>Error</h3><ul>";
    foreach ($errors as $e) {
        echo "<li>" . htmlspecialchars($e) . "</li>";
    }
    echo "</ul>";
    echo "</section></main>";
    echo "<footer><small>CPSC 2221 – Carpooling Project</small></footer>";
    echo "</body></html>";
    exit;
}

$check = $conn->prepare("SELECT * FROM StudentUser WHERE StudentID = ?");
$check->bind_param("i", $studentID);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo "<h3>Error</h3>";
    echo "<p>No student found with ID <strong>" . htmlspecialchars($studentID) . "</strong>.</p>";
    echo "</section></main>";
    echo "<footer><small>CPSC 2221 – Carpooling Project</small></footer>";
    echo "</body></html>";
    exit;
}

// --- Perform the deletion ---
$stmt = $conn->prepare("DELETE FROM StudentUser WHERE StudentID = ?");
$stmt->bind_param("i", $studentID);

echo "<h3>Delete Result</h3>";

if (!$stmt->execute()) {
    echo "<p style='color:red;'><strong>Error during deletion:</strong></p>";
    echo "<pre>" . htmlspecialchars($stmt->error) . "</pre>";
} else {
    echo "<p><strong>Delete Successful!</strong></p>";
    echo "<p>Student ID <strong>" . htmlspecialchars($studentID) . "</strong> has been removed.</p>";
    echo "<p>All related records (rider/provider, schedules, relations) were automatically removed via <strong>CASCADE</strong>.</p>";
}

$stmt->close();
$conn->close();
?>

</section>

<div class="mt-3">
    <a href="delete.html" class="btn-orange">Delete Another</a>
    <a href="index.html" class="btn-orange ms-2">Back to Home</a>
</div>

</main>

<footer>
    <small>CPSC 2221 – Carpooling Project</small>
</footer>

</body>
</html>
