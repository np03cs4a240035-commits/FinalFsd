<?php
include('../config/db.php');
$conn->query("ALTER TABLE grades DROP COLUMN subject");
echo "Dropped subject column from grades.";
?>