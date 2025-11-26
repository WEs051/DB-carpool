<?php

require 'db.php';

if (!isset($conn)) {
    die("Database connection (\$conn) is not set. Please check db.php.");
}

// Get form input
$studentID = $_POST['studentID'] ?? null;
$newHeight = $_POST['height'] ?? null;
$newEmail  = $_POST['email'] ?? null;

$errors = [];

// Validate
if (!$studentID) {
    $errors[] = "Student ID is required.";
}

if (!$newHeight && !$newEmail) {
    $errors[] = "Please provide a new height or new email to update.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Student</title>
    <link rel="stylesheet" href="index.css">
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

<main>
<section>
<?php

if (!empty($errors)) {
    echo "<h3>Error</h3><ul>";
    foreach ($errors as $e) {
        echo "<li>" . htmlspecialchars($e) . "</li>";
    }
    echo "</ul></section></main></body></html>";
    exit;
}

$updateFields = [];
$params = [];

if ($newHeight) {
    $updateFields[] = "Height = ?";
    $params[] = $newHeight;
}

if ($newEmail) {
    $updateFields[] = "StudentName = StudentName, AddressID = AddressID, PostalCode = PostalCode";
}

if ($newEmail) {
    echo "<p style='color:red;'>Warning: Your database schema does NOT have an Email column in StudentUser. Email cannot be updated.</p>";
}
if ($newHeight) {
    $stmt = $conn->prepare("UPDATE StudentUser SET Height = ? WHERE StudentID = ?");
    $stmt->bind_param("di", $newHeight, $studentID);
}

if (!$stmt->execute()) {
    echo "<h3>Error during update:</h3> " . htmlspecialchars($stmt->error);
} else {
    echo "<h3>Update Successful</h3>";
    echo "<p>Student ID <strong>" . htmlspecialchars($studentID) . "</strong> has been updated.";
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
