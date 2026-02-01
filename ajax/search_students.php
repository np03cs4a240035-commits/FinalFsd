<?php
include('../config/db.php');

$q = $_GET['q'] ?? '';
$type = $_GET['type'] ?? 'student';

$conn->real_escape_string($q);

if ($type == 'student') {
    $sql = "SELECT s.* FROM students s 
            WHERE s.name LIKE '%$q%' OR s.email LIKE '%$q%' OR s.course LIKE '%$q%'";

    $r = $conn->query($sql);
    echo "<table border='0'><tr><th>Name</th><th>Course</th><th>Email</th><th>Action</th></tr>";
    while ($s = $r->fetch_assoc()) {
        echo "<tr>
            <td>{$s['name']}</td>
            <td>{$s['course']}</td>
            <td>{$s['email']}</td>
            <td>
                <a href='students.php?edit={$s['id']}' style='color:var(--dark-pink);font-weight:bold;margin-right:10px;'>Edit</a>
                <a href='students.php?del={$s['id']}' style='color:red;font-size:12px' onclick=\"return confirm('Delete?')\">Delete</a>
            </td>
        </tr>";
    }
    echo "</table>";

} elseif ($type == 'attendance') {
    // 1. SUMMARY TABLE (Aggregate)
    $sum_q = "SELECT s.id as sid, s.name as student_name, s.course, 
                     a.total_classes, a.total_present 
              FROM students s 
              LEFT JOIN attendance a ON s.id = a.student_id 
              WHERE s.name LIKE '%$q%' OR s.course LIKE '%$q%'";

    echo "<hr style='margin: 30px 0; border: 0; border-top: 1px solid #ffe6f2;'>";
    echo "<h3>Attendance Summary 📊</h3>";
    echo "<table border='0' style='margin-bottom: 30px;'>";
    echo "<tr><th>Student</th><th>Course</th><th>Total Classes</th><th>Present</th><th>Absent</th><th>Percentage</th><th>Action</th></tr>";

    $sum_r = $conn->query($sum_q);
    if ($sum_r->num_rows > 0) {
        while ($row = $sum_r->fetch_assoc()) {
            $total = $row['total_classes'] ?? 0;
            $present = $row['total_present'] ?? 0;
            $absent = $total - $present;
            $percent = $total > 0 ? round(($present / $total) * 100, 1) : 0;
            $color = $percent < 75 ? 'red' : 'green';
            echo "<tr>
                <td>{$row['student_name']}</td>
                <td>{$row['course']}</td>
                <td>{$total}</td>
                <td>{$present}</td>
                <td>{$absent}</td>
                <td style='color:$color; font-weight:bold;'>{$percent}%</td>
                <td><a href='attendance.php?edit={$row['sid']}' style='color:var(--dark-pink);font-weight:bold;'>Edit</a></td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No records found</td></tr>";
    }
    echo "</table>";
} elseif ($type == 'grades') {
    $sql = "SELECT g.*, s.name as student_name, m.name as module_name 
            FROM grades g 
            JOIN students s ON g.student_id = s.id 
            LEFT JOIN modules m ON g.module_id = m.id
            WHERE s.name LIKE '%$q%' OR m.name LIKE '%$q%'";

    $r = $conn->query($sql);
    echo "<table border='0'><tr><th>Student</th><th>Module</th><th>Grade</th><th>Action</th></tr>";
    while ($row = $r->fetch_assoc()) {
        echo "<tr>
            <td>{$row['student_name']}</td>
            <td>" . ($row['module_name'] ?? 'N/A') . "</td>
            <td>{$row['grade']}</td>
            <td>
                <a href='grades.php?edit={$row['id']}' style='color:var(--dark-pink);font-weight:bold;margin-right:5px'>Edit</a>
                <a href='grades.php?del={$row['id']}' style='color:red;font-size:12px' onclick=\"return confirm('Delete?')\">Delete</a>
            </td>
        </tr>";
    }
    echo "</table>";
}
?>