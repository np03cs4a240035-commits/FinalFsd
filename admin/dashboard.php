<?php
include('../config/db.php');
if (!isset($_SESSION['user']))
    header('Location: ../auth/login.php');
?>
<!DOCTYPE html>
<html>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>
    <div class="container" style="width: 850px; max-width:95%;">
        <h2 class="user-welcome">Welcome <span><?= $_SESSION['user'] ?></span> 🌸</h2>
        <div class="nav-grid">
            <a href="students.php" class="nav-item">📚 Students</a>
            <a href="manage_modules.php" class="nav-item">📘 Modules</a>
            <a href="manage_courses.php" class="nav-item">🎓 Courses</a>
            <a href="attendance.php" class="nav-item">📅 Attendance</a>
            <a href="grades.php" class="nav-item">🎓 Grades</a>
            <?php if (($_SESSION['role'] ?? '') == 'superadmin'): ?>
                <a href="manage_admins.php" class="nav-item" style="background:#fff0f5;color:#d63384;">🛡️ Manage Admins</a>
            <?php endif; ?>
            <a href="../auth/logout.php" class="nav-item" style="color:var(--dark-pink)">🚪 Logout</a>
        </div>
    </div>
</body>

</html>