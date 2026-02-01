<?php
include('../config/db.php');
if (!isset($_SESSION['user']))
    header('Location: ../auth/login.php');

// ADD STUDENT
if (isset($_POST['add'])) {
    $check = $conn->prepare("SELECT id FROM students WHERE email=?");
    $check->bind_param("s", $_POST['email']);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $err = "Email already exists! ❌";
    } else {
        // REMOVED module_id from insert
        $stmt = $conn->prepare("INSERT INTO students(name,course,email) VALUES (?,?,?)");
        $stmt->bind_param("sss", $_POST['name'], $_POST['course'], $_POST['email']);
        $stmt->execute();
        $msg = "Student Added! 🌸";
    }
}

// UPDATE STUDENT
if (isset($_POST['update'])) {
    // REMOVED module_id from update
    $stmt = $conn->prepare("UPDATE students SET name=?, course=?, email=? WHERE id=?");
    $stmt->bind_param("sssi", $_POST['name'], $_POST['course'], $_POST['email'], $_GET['edit']);
    $stmt->execute();
    header('Location: students.php');
}

// FETCH ID FOR EDIT
$edit_row = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_row = $conn->query("SELECT * FROM students WHERE id=$id")->fetch_assoc();
}

// DELETE STUDENT
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $conn->query("DELETE FROM students WHERE id=$id");
    header('Location: students.php');
}
?>
<!DOCTYPE html>
<html>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <script>
        function searchStudent(q) {
            fetch('../ajax/search_students.php?type=student&q=' + q)
                .then(res => res.text())
                .then(data => {
                    document.getElementById('table-container').innerHTML = data;
                });
        }
    </script>
</head>

<body>
    <div class="container" style="width: 900px; max-width:95%;">
        <h2>Students 📚</h2>
        <?php
        if (isset($err))
            echo "<p style='color:red;background:#ffe6e6;padding:10px;border-radius:10px;'>$err</p>";
        if (isset($msg))
            echo "<p style='color:green;background:#e6ffe6;padding:10px;border-radius:10px;'>$msg</p>";
        ?>

        <form method="POST">
            <input name="name" placeholder="Student Name" value="<?= $edit_row['name'] ?? '' ?>" required>
            <select name="course" required>
                <option value="">Select Course</option>
                <?php
                // UPDATED to fetch from courses table
                $courses_res = $conn->query("SELECT * FROM courses");
                while ($c = $courses_res->fetch_assoc()) {
                    // Check logic adapted for object
                    $sel = ($edit_row['course'] ?? '') == $c['name'] ? 'selected' : '';
                    echo "<option value='{$c['name']}' $sel>{$c['name']}</option>";
                }
                ?>
            </select>

            <!-- REMOVED Module Select Input -->

            <input name="email" placeholder="Email" value="<?= $edit_row['email'] ?? '' ?>" required>

            <?php if ($edit_row): ?>
                <button name="update">Update Student</button>
                <a href="students.php"
                    style="display:block;margin-top:10px;text-align:center;color:var(--dark-pink);">Cancel</a>
            <?php else: ?>
                <button name="add">Add Student</button>
            <?php endif; ?>
        </form>

        <?php if (!$edit_row): ?>
            <input onkeyup="searchStudent(this.value)" placeholder="Search Name or Course... 🔍" style="margin-top:20px;">
        <?php endif; ?>

        <div id="table-container">
            <table border="0">
                <tr>
                    <th>Name</th>
                    <!-- REMOVED Module Column -->
                    <th>Course</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
                <?php
                // REMOVED JOIN with modules
                $r = $conn->query("SELECT * FROM students");
                while ($s = $r->fetch_assoc()) {
                    echo "<tr>
                    <td>{$s['name']}</td>
                    <td>{$s['course']}</td>
                    <td>{$s['email']}</td>
                    <td>
                        <a href='?edit={$s['id']}' style='color:var(--dark-pink);font-weight:bold;margin-right:10px;'>Edit</a>
                        <a href='?del={$s['id']}' style='color:red;font-size:12px' onclick=\"return confirm('Delete?')\">Delete</a>
                    </td>
                </tr>";
                }
                ?>
            </table>
        </div>

        <br>
        <a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>
        <a href="../auth/logout.php" class="back-link" style="margin-left: 10px;">Logout</a>
    </div>
</body>

</html>