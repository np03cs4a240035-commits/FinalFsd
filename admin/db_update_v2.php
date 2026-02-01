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

// 1. MODULES Description
addCol($conn, 'modules', 'description', 'TEXT');

// 2. STUDENTS Module Link
addCol($conn, 'students', 'module_id', 'INT');

echo "Database Schema V2 Updated!";
?>