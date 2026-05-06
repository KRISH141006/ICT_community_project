<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// If profile already exists, go to dashboard
$chk = $conn->prepare("SELECT id FROM profiles WHERE user_id = ?");
$chk->bind_param("i", $user_id);
$chk->execute();
if ($chk->get_result()->num_rows > 0) {
    header("Location: dashboard.php");
    exit;
}

$error = $_SESSION['profile_error'] ?? '';
unset($_SESSION['profile_error']);

$page_title = "Complete Profile";
$show_nav   = false;
include __DIR__ . '/../includes/header.php';
?>
<div class="page-wrap narrow" style="display:flex;flex-direction:column;justify-content:center;min-height:calc(100vh - 58px)">

  <div style="margin-bottom:2rem;">
    <h1 class="page-title">Complete your profile</h1>
    <p class="page-subtitle">Just a few details before you get started.</p>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="card">
    <form action="../actions/save_profile.php" method="POST">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" placeholder="e.g. Krish Sondagar" required>
      </div>
      <div class="form-group">
        <label for="branch">Branch / Department</label>
        <input type="text" id="branch" name="branch" placeholder="e.g. Computer Engineering" required>
      </div>
      <div class="form-group">
        <label for="skills">Skills <span class="text-muted">(optional)</span></label>
        <textarea id="skills" name="skills" placeholder="e.g. Python, React, MySQL, ..."></textarea>
      </div>
      <button type="submit" class="btn btn-primary btn-full">Save &amp; Continue →</button>
    </form>
  </div>
</div>
</body>
</html>
