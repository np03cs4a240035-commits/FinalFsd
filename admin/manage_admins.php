<?php
include('../config/db.php');
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'superadmin') {
    header('Location: dashboard.php');
    exit();
}

// DELETE
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    // Protect superadmin from deletion
    $check = $conn->query("SELECT role FROM users WHERE id=$id")->fetch_assoc();
    if ($check && $check['role'] !== 'superadmin') {
        $conn->query("DELETE FROM users WHERE id=$id");
    }
    header('Location: manage_admins.php');
    exit();
}

// ADD
$msg = '';
if (isset($_POST['add'])) {
    $u = $_POST['username'];
    // Check duplicate
    $check = $conn->prepare("SELECT id FROM users WHERE username=?");
    $check->bind_param("s", $u);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $err = "Username already exists!";
    } elseif ($_POST['password'] !== $_POST['confirm_password']) {
        $err = "Passwords do not match!";
    } else {
        $p = hash('sha256', $_POST['password']);
        $stmt = $conn->prepare("INSERT INTO users(username, password, role) VALUES (?, ?, 'admin')");
        $stmt->bind_param("ss", $u, $p);
        if ($stmt->execute()) {
            $msg = "Admin Created Successfully! ✨";
        } else {
            $err = "Error creating admin";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <div class="container" style="width: 850px; max-width:95%;">
        <h2>Manage Admins 🛡️</h2>

        <?php if (isset($err))
            echo "<p style='color:red'>$err</p>"; ?>
        <?php if ($msg)
            echo "<p style='color:green'>$msg</p>"; ?>

        <form method="POST">
            <h3>Create New Admin</h3>
            <input name="username" placeholder="Username" required>
            <input name="password" type="password" placeholder="Password" required>
            <input name="confirm_password" type="password" placeholder="Confirm Password" required>

            <button name="add">Create Admin</button>
        </form>

        <hr>

        <h3>Existing Admins</h3>
        <table border="0">
            <th>Username</th>

            <th>Action</th>
            <?php
            $r = $conn->query("SELECT * FROM users WHERE role='admin'");
            while ($row = $r->fetch_assoc()):
                ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($row['username']) ?>
                    </td>

                    <td>
                        <a href="?del=<?= $row['id'] ?>" style="color:red;font-size:12px"
                            onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <br>
        <a href="dashboard.php" class="back-link">⬅ Back to Dashboard</a>
    </div>
</body>

</html>