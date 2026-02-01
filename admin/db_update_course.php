<?php
include('../config/db.php');

// Add course_id column
$sql1 = "ALTER TABLE users ADD COLUMN course_id INT DEFAULT NULL";
if ($conn->query($sql1) === TRUE) {
    echo "Column course_id added successfully.<br>";
} else {
    echo "Error adding column: " . $conn->error . "<br>";
}

// Remove module_id column
$sql2 = "ALTER TABLE users DROP COLUMN module_id";
if ($conn->query($sql2) === TRUE) {
    echo "Column module_id dropped successfully.<br>";
} else {
    echo "Error dropping column: " . $conn->error . "<br>";
}

echo "Database update completed.";
?>