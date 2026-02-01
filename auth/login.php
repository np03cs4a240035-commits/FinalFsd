<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../config/db.php');

if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = hash('sha256', $_POST['password']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();
    $r = $stmt->get_result();
    if ($r->num_rows == 1) {
        $row = $r->fetch_assoc();
        $_SESSION['user'] = $row['username'];
        $_SESSION['role'] = $row['role'];
        header('Location: ../admin/dashboard.php');
        exit();
    } else {
        $err = "Invalid login";
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
    <div class="container" style="width: 700px; max-width:95%;">
        <h2>Student Management System 🌸</h2>
        <?php if (isset($err))
            echo "<p style='color:red'>$err</p>"; ?>
        <form method="POST">
            <input name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button name="login">Login</button>
        </form>
    </div>
</body>

</html>