<?php
include('../config/db.php');
$r = $conn->query("SELECT DISTINCT role FROM users");
while ($row = $r->fetch_assoc()) {
    echo "Role: " . $row['role'] . "\n";
}
?>