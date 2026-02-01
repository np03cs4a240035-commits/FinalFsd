<?php
include('../config/db.php');
if (!isset($_SESSION['user']))
    header('Location: ../auth/login.php');

// UPDATE/INSERT ATTENDANCE
if (isset($_POST['update_attendance'])) {
    $student_id = $_POST['student'];
    $total = $_POST['total'];
    $present = $_POST['present'];

    // Using INSERT ... ON DUPLICATE KEY UPDATE to handle both cases efficiently
    $stmt = $conn->prepare("INSERT INTO attendance (student_id, total_classes, total_present) VALUES (?, ?, ?) 
                            ON DUPLICATE KEY UPDATE total_classes = VALUES(total_classes), total_present = VALUES(total_present)");
    $stmt->bind_param("iii", $student_id, $total, $present);
    $stmt->execute();
    $msg = "Attendance Updated! 🌸";
}

// DELETE ATTENDANCE
if (isset($_GET['del'])) {
    $sid = $_GET['del'];
    $conn->query("DELETE FROM attendance WHERE student_id=$sid");
    header('Location: attendance.php');
    exit();
}

// EDIT FETCH
$edit_row = null;
if (isset($_GET['edit'])) {
    // If editing, we just want to pre-fill the form for a specific student ID
    // Note: The UI logic acts more like "Update this student" always, but let's support clicking 'edit' from table
    $student_id = $_GET['edit'];
    $r = $conn->query("SELECT * FROM attendance WHERE student_id=$student_id");
    if ($r->num_rows > 0) {
        $edit_row = $r->fetch_assoc();
    } else {
        // If no attendance record yet, just set ID so dropdown selects it
        $edit_row = ['student_id' => $student_id, 'total_classes' => '', 'total_present' => ''];
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <script>
        function searchAttendance(q) {
            fetch('../ajax/search_students.php?type=attendance&q=' + q)
                .then(res => res.text())
                .then(data => {
                    document.getElementById('full-result').innerHTML = data;
                });
        }
    </script>
</head>

<body>
    <div class="container" style="width: 900px; max-width:95%;">
        <h2>Attendance 📅</h2>
        <?php if (isset($msg))
            echo "<p style='color:green;background:#e6ffe6;padding:10px;border-radius:10px;'>$msg</p>"; ?>

        <form method="POST">
            <label>Student:</label>
            <select name="student" required>
                <option value="">Select Student</option>
                <?php
                $r = $conn->query("SELECT * FROM students");
                while ($s = $r->fetch_assoc()) {
                    $sel = ($edit_row && ($edit_row['student_id'] ?? $edit_row['id'] ?? 0) == $s['id']) ? 'selected' : '';
                    echo "<option value='{$s['id']}' $sel>{$s['name']} ({$s['course']})</option>";
                }
                ?>
            </select>

            <label>Total Classes:</label>
            <input type="number" name="total" placeholder="Total Classes Conducted"
                value="<?= $edit_row['total_classes'] ?? '' ?>" required>

            <label>Total Present:</label>
            <input type="number" name="present" placeholder="Total Classes Attended"
                value="<?= $edit_row['total_present'] ?? '' ?>" required>

            <button name="update_attendance" style="margin-top:20px;">Update Attendance</button>
        </form>

        <input onkeyup="searchAttendance(this.value)" placeholder="Search Student... 🔍" style="margin-top:20px;">

        <div id="full-result">
            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ffe6f2;">

            <h3>Attendance Summary 📊</h3>
            <table border="0" style="margin-bottom: 30px;">
                <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Total Classes</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th></th>Percentage</th>
                    <th>Action</th>
                </tr>
                <?php
                // Fetch All with Left Join (show students even if no attendance record yet)
                $sum_q = "SELECT s.id as sid, s.name as student_name, s.course, 
                                 a.total_classes, a.total_present 
                          FROM students s 
                          LEFT JOIN attendance a ON s.id = a.student_id";
                $sum_r = $conn->query($sum_q);
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
                        <td>
                            <a href='?edit={$row['sid']}' style='color:var(--dark-pink);font-weight:bold; margin-right:5px;'>Edit</a>
                            <a href='?del={$row['sid']}' style='color:red;font-size:12px' onclick=\"return confirm('Delete attendance record?')\">Delete</a>
                        </td>
                    </tr>";
                }
                ?>
            </table>

            <!-- REmoved Daily Records Table -->
        </div>

        <br>
        <a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>
        <a href="../auth/logout.php" class="back-link" style="margin-left: 10px;">Logout</a>
    </div>
</body>

</html>