<?php
include('../config/db.php');
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

// ADD COURSE
$msg = '';
if (isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO courses(name, description) VALUES (?,?)");
    $stmt->bind_param("ss", $_POST['name'], $_POST['description']);
    if ($stmt->execute())
        $msg = "Course Created! ✨";
}

// DELETE COURSE
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $conn->query("DELETE FROM courses WHERE id=$id");
    header('Location: manage_courses.php');
    exit();
}

// EDIT COURSE
$edit_row = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_row = $conn->query("SELECT * FROM courses WHERE id=$id")->fetch_assoc();
}

// UPDATE COURSE
if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE courses SET name=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $_POST['name'], $_POST['description'], $_GET['edit']);
    $stmt->execute();
    header('Location: manage_courses.php');
    exit();
}
?>
<!DOCTYPE html>
<html>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <div class="container" style="width: 900px; max-width:95%;">
        <h2>Manage Courses 🎓</h2>
        <?php if ($msg)
            echo "<p style='color:green;background:#e6ffe6;padding:10px;border-radius:10px;'>$msg</p>"; ?>

        <form method="POST">
            <input name="name" placeholder="Course Name (e.g. BIT, BBA)" value="<?= $edit_row['name'] ?? '' ?>"
                required>
            <textarea name="description" placeholder="Course Description" rows="3"
                style="width:100%;padding:10px;margin:10px 0;border-radius:5px;border:1px solid #ddd;"><?= $edit_row['description'] ?? '' ?></textarea>

            <?php if ($edit_row): ?>
                <button name="update">Update Course</button>
                <a href="manage_courses.php"
                    style="display:block;text-align:center;margin-top:10px;color:var(--dark-pink)">Cancel</a>
            <?php else: ?>
                <button name="add">Create Course</button>
            <?php endif; ?>
        </form>

        <br>
        <table border="0">
            <tr>
                <th>ID</th>
                <th>Course Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            <?php
            $r = $conn->query("SELECT * FROM courses");
            while ($row = $r->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td><b>{$row['name']}</b></td>
                    <td>{$row['description']}</td>
                    <td>
                        <a href='?edit={$row['id']}' style='color:var(--dark-pink);font-weight:bold;margin-right:5px'>Edit</a>
                        <a href='?del={$row['id']}' style='color:red;font-size:12px' onclick=\"return confirm('Delete?')\">Delete</a>
                    </td>
                </tr>";
            }
            ?>
        </table>

        <br>
        <a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>
    </div>
</body>

</html>