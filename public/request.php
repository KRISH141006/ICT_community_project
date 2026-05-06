<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

if ($_SESSION['role'] !== 'student') {
    header("Location: dashboard.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Ensure profile
$chk = $conn->prepare("SELECT id FROM profiles WHERE user_id = ?");
$chk->bind_param("i", $user_id);
$chk->execute();
if ($chk->get_result()->num_rows === 0) {
    header("Location: profile.php");
    exit;
}

$error   = $_SESSION['req_error']   ?? ''; unset($_SESSION['req_error']);
$success = $_SESSION['req_success'] ?? ''; unset($_SESSION['req_success']);

// Previous requests
$stmt = $conn->prepare("SELECT * FROM requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$myRequests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$page_title = "Request Skill Test";
include __DIR__ . '/../includes/header.php';
?>

<div class="page-wrap medium">
  <h1 class="page-title">Request a Skill Test</h1>
  <p class="page-subtitle">Submit a skill you'd like to be evaluated on by a reviewer.</p>

  <?php if ($error):   ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <div class="card">
    <form action="../actions/submit_request.php" method="POST">
      <div class="form-group">
        <label for="skill">Skill to be tested</label>
        <input type="text" id="skill" name="skill" placeholder="e.g. Python, Data Structures, React…" required>
      </div>
      <button type="submit" class="btn btn-primary">Submit Request</button>
    </form>
  </div>

  <?php if (!empty($myRequests)): ?>
  <h2 style="font-size:1rem;font-weight:600;color:var(--text);margin:2rem 0 0.75rem;">Previous Requests</h2>
  <div class="card" style="padding:0;overflow:hidden;">
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Skill</th><th>Status</th><th>Date</th></tr>
        </thead>
        <tbody>
          <?php foreach ($myRequests as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['skill']) ?></td>
            <td><span class="badge badge-<?= $r['status'] ?>"><?= $r['status'] ?></span></td>
            <td class="text-muted text-sm"><?= date('d M Y', strtotime($r['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>
</body>
</html>
