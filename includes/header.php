<?php
// includes/header.php
// Usage: include with $page_title set beforehand
$page_title = $page_title ?? 'ICT Community';
$show_nav   = $show_nav ?? true;
$nav_role   = $_SESSION['role'] ?? '';
$user_id    = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?> — ICT Community</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="site-header">
  <div class="header-inner">
    <a href="dashboard.php" class="site-logo">ICT<span>.</span>Community</a>
    <?php if ($show_nav && $user_id): ?>
    <nav class="header-nav">
      <a href="dashboard.php" class="nav-link">Dashboard</a>
      <?php if ($nav_role === 'student'): ?>
        <a href="request.php" class="nav-link">Request Test</a>
      <?php endif; ?>
      <?php if (in_array($nav_role, ['senior','faculty','hod'])): ?>
        <a href="reviewer_dashboard.php" class="nav-link">Review</a>
      <?php endif; ?>
      <a href="../actions/logout.php" class="nav-link">Logout</a>
    </nav>
    <?php endif; ?>
  </div>
</header>
