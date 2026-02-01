<?php
include('../config/db.php');
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit();
}

// ADD MODULE
$msg = '';
if (isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO modules(name, description) VALUES (?,?)");
    $stmt->bind_param("ss", $_POST['name'], $_POST['description']);
    if ($stmt->execute())
        $msg = "Module Created! ✨";
}

// DELETE MODULE
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $conn->query("DELETE FROM modules WHERE id=$id");
    header('Location: manage_modules.php');
    exit();
}

// EDIT MODULE (Simple inline or redirect, I'll do inline handling via GET)
$edit_row = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_row = $conn->query("SELECT * FROM modules WHERE id=$id")->fetch_assoc();
}

// UPDATE MODULE
if (isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE modules SET name=?, description=? WHERE id=?");
    $stmt->bind_param("ssi", $_POST['name'], $_POST['description'], $_GET['edit']);
    $stmt->execute();
    header('Location: manage_modules.php');
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
        <h2>Manage Modules 📚</h2>
        <?php if ($msg)
            echo "<p style='color:green;background:#e6ffe6;padding:10px;border-radius:10px;'>$msg</p>"; ?>

        <form method="POST">
            <input name="name" placeholder="Module Name (e.g. Web Development)" value="<?= $edit_row['name'] ?? '' ?>"
                required>
            <textarea name="description" placeholder="Description / Course Details" rows="3"
                style="width:100%;padding:10px;margin:10px 0;border-radius:5px;border:1px solid #ddd;"><?= $edit_row['description'] ?? '' ?></textarea>

            <?php if ($edit_row): ?>
                <button name="update">Update Module</button>
                <a href="manage_modules.php"
                    style="display:block;text-align:center;margin-top:10px;color:var(--dark-pink)">Cancel</a>
            <?php else: ?>
                <button name="add">Create Module</button>
            <?php endif; ?>
        </form>

        <br>
        <table border="0">
            <tr>
                <th>ID</th>
                <th>Module Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            <?php
            $r = $conn->query("SELECT * FROM modules");
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