<?php
$conn = new mysqli('localhost', 'root', '', 'student_db');
// $conn = new mysqli('localhost', 'NP03CS4A240035', 'P6DvkRL08o', 'NP03CS4A240035');

if ($conn->connect_error) {
    die('DB Error');
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>