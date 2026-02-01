<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../config/db.php');

function addCol($conn, $table, $col, $def)
{
    try {
        $r = $conn->query("SHOW COLUMNS FROM $table LIKE '$col'");
        if ($r && $r->num_rows == 0) {
            $conn->query("ALTER TABLE $table ADD COLUMN $col $def");
            echo "Added $col to $table.<br>\n";
        }
    } catch (Exception $e) {
        echo "Error adding $col: " . $e->getMessage() . "<br>\n";
    }
}

// 1. MODULES
$conn->query("CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
)");
// Seed
$c = $conn->query("SELECT COUNT(*) as c FROM modules")->fetch_assoc()['c'];
if ($c == 0) {
    $conn->query("INSERT INTO modules (name) VALUES ('Group A'), ('Group B'), ('Group C'), ('Module 101'), ('Module 102')");
    echo "Seeded modules.<br>\n";
}

// 2. COLUMNS
addCol($conn, 'attendance', 'module_id', 'INT');
addCol($conn, 'grades', 'module_id', 'INT');
addCol($conn, 'users', 'module_id', 'INT');

echo "Database Update Complete!";
?>