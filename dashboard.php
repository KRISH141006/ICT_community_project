<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>

<h1>Welcome</h1>
<p>User ID: <?php echo $_SESSION['user_id']; ?></p>
<p>Role: <?php echo $_SESSION['role']; ?></p>

<a href="logout.php">Logout</a>