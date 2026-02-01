<?php
include('../config/db.php');
$r = $conn->query("DESCRIBE attendance");
while ($row = $r->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>