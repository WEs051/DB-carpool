<?php
require 'db.php';

if (!isset($conn)) {
    die("Database connection (\$conn) is not set. Please check db.php.");
}

$studentID = $_POST['studentID'] ?? null;
$newHeight = $_POST['height'] ?? null;

$errors = [];

// Validate
if (!$studentID) {
    $errors[] = "Student ID is required.";
}

if (!$newHeight) {
    $errors[] = "Please enter a new height to update.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Student</title>

    <link rel="stylesheet" href="index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<header>
    <h1>Update Student Information</h1>
    <nav>
        <a href="index.html">Home</a>
        <a href="rider.html">Register as Rider</a>
        <a href="provider.html">Register as Provider</a>
        <a href="match.html">Find Matches</a>
    </nav>
</header>

<main class="container my-4">
<section>
<?php

// Show validation errors
if (!empty($errors)) {
    echo "<div class='alert alert-danger'><ul>";
    foreach ($errors as $e) {
        echo "<li>" . htmlspecialchars($e) . "</li>";
    }
    echo "</ul></div>";
    echo "</section></main></body></html>";
    exit;
}

// Check if student exists
$check = $conn->prepare("SELECT * FROM StudentUser WHERE StudentID = ?");
$check->bind_param("i", $studentID);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>
            No student found with ID <strong>" . htmlspecialchars($studentID) . "</strong>.
          </div>";
    exit("</section></main></body></html>");
}

$check->close();

// Update height only
$stmt = $conn->prepare("UPDATE StudentUser SET Height = ? WHERE StudentID = ?");
$stmt->bind_param("di", $newHeight, $studentID);

if (!$stmt->execute()) {
    echo "<div class='alert alert-danger'>
            Error during update: " . htmlspecialchars($stmt->error) . "
          </div>";
} else {
    echo "<div class='alert alert-success'>
            <h4>Update Successful</h4>
            Student ID <strong>" . htmlspecialchars($studentID) . "</strong> was updated.<br>
            New height: <strong>" . htmlspecialchars($newHeight) . "</strong> cm.
          </div>";

    echo '<a href="update.html" class="btn btn-secondary me-2">Update Another</a>';
    echo '<a href="index.html" class="btn btn-primary">Back to Home</a>';
}

$stmt->close();
$conn->close();
?>
</section>
</main>

<footer>
    <small>CPSC 2221 – Carpooling Project</small>
</footer>

</body>
</html>
