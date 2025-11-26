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


$check = $conn->prepare("SELECT * FROM StudentUser WHERE StudentID = ?");
$check->bind_param("i", $studentID);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    echo "<h3>Error</h3>";
    echo "<p>No student found with ID <strong>" . htmlspecialchars($studentID) . "</strong>.</p>";
    echo "</section></main></body></html>";
    exit;
}


$stmt = $conn->prepare("DELETE FROM StudentUser WHERE StudentID = ?");
$stmt->bind_param("i", $studentID);

if (!$stmt->execute()) {
    echo "<h3>Error during deletion:</h3> " . htmlspecialchars($stmt->error);
} else {
    echo "<h3>Delete Successful</h3>";
    echo "<p>Student ID <strong>" . htmlspecialchars($studentID) . "</strong> has been removed.</p>";
    echo "<p>All related records (rider/provider, schedules, relations) were automatically deleted via cascade.</p>";
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
