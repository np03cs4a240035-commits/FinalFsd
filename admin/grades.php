<?php
include('../config/db.php');
if (!isset($_SESSION['user']))
    header('Location: ../auth/login.php');

// INSERT GRADE
if (isset($_POST['add'])) {
    // REMOVED subject from insert
    $stmt = $conn->prepare("INSERT INTO grades(student_id,grade,module_id) VALUES (?,?,?)");
    $stmt->bind_param("isi", $_POST['student'], $_POST['grade'], $_POST['module']);
    $stmt->execute();
    $msg = "Grade Submitted! 🌟";
}

// DELETE GRADE
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $conn->query("DELETE FROM grades WHERE id=$id");
    header('Location: grades.php');
    exit();
}

// UPDATE GRADE
if (isset($_POST['update'])) {
    // REMOVED subject from update
    $stmt = $conn->prepare("UPDATE grades SET grade=?, module_id=? WHERE id=?");
    $stmt->bind_param("sii", $_POST['grade'], $_POST['module'], $_GET['edit']);
    $stmt->execute();
    header('Location: grades.php');
    exit();
}

// FETCH EDIT ROW
$edit_row = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_row = $conn->query("SELECT * FROM grades WHERE id=$id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <script>
        function searchGrades(q) {
            fetch('../ajax/search_students.php?type=grades&q=' + q)
                .then(res => res.text())
                .then(data => {
                    document.getElementById('table-container').innerHTML = data;
                });
        }
    </script>
</head>

<body>
    <div class="container" style="width: 900px; max-width:95%;">
        <h2><?= $edit_row ? 'Edit Grade' : 'Add Grade' ?> 🎓</h2>
        <?php if (isset($msg))
            echo "<p style='color:green;background:#e6ffe6;padding:10px;border-radius:10px;'>$msg</p>"; ?>

        <form method="POST">
            <?php if (!$edit_row): ?>
                <select name="student" required>
                    <option value="">Select Student</option>
                    <?php $r = $conn->query("SELECT * FROM students");
                    while ($s = $r->fetch_assoc())
                        echo "<option value='{$s['id']}'>{$s['name']}</option>"; ?>
                </select>
            <?php endif; ?>

            <select name="module" required>
                <option value="">Select Module/Group</option>
                <?php
                $modules = $conn->query("SELECT * FROM modules");
                while ($m = $modules->fetch_assoc()) {
                    $sel = ($edit_row && $edit_row['module_id'] == $m['id']) ? 'selected' : '';
                    echo "<option value='{$m['id']}' $sel>{$m['name']}</option>";
                }
                ?>
            </select>

            <!-- REMOVED Subject Input -->

            <select name="grade">
                <?php
                $grades = ['A', 'B', 'C', 'D', 'F'];
                foreach ($grades as $g) {
                    $sel = ($edit_row && ($edit_row['grade'] ?? '') == $g) ? 'selected' : '';
                    echo "<option $sel>$g</option>";
                }
                ?>
            </select>

            <?php if ($edit_row): ?>
                <button name="update">Update Grade</button>
                <a href="grades.php"
                    style="display:block;text-align:center;margin-top:10px;color:var(--dark-pink)">Cancel</a>
            <?php else: ?>
                <button name="add">Submit Grade</button>
            <?php endif; ?>
        </form>

        <?php if (!$edit_row): ?>
            <input onkeyup="searchGrades(this.value)" placeholder="Search Student or Module... 🔍" style="margin-top:20px;">
        <?php endif; ?>

        <div id="table-container">
            <table border="0">
                <tr>
                    <th>Student</th>
                    <th>Module</th>
                    <!-- REMOVED Subject Column -->
                    <th>Grade</th>
                    <th>Action</th>
                </tr>
                <?php
                $r = $conn->query("SELECT g.*, s.name as student_name, m.name as module_name 
                                   FROM grades g 
                                   JOIN students s ON g.student_id = s.id 
                                   LEFT JOIN modules m ON g.module_id = m.id");
                while ($row = $r->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['student_name']}</td>
                        <td>" . ($row['module_name'] ?? 'N/A') . "</td>
                        <td>{$row['grade']}</td>
                        <td>
                            <a href='?edit={$row['id']}' style='color:var(--dark-pink);font-weight:bold;margin-right:5px'>Edit</a>
                            <a href='?del={$row['id']}' style='color:red;font-size:12px' onclick=\"return confirm('Delete?')\">Delete</a>
                        </td>
                    </tr>";
                }
                ?>
            </table>
        </div>

        <br>
        <a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>
        <a href="../auth/logout.php" class="back-link" style="margin-left:10px">Logout</a>
    </div>
</body>

</html>