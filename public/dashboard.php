<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$role    = $_SESSION['role'];

// Ensure profile exists (redirect if not)
$chk = $conn->prepare("SELECT * FROM profiles WHERE user_id = ?");
$chk->bind_param("i", $user_id);
$chk->execute();
$profile = $chk->get_result()->fetch_assoc();

if (!$profile) {
    header("Location: profile.php");
    exit;
}

// Fetch this user's requests (for students) or all requests summary (for reviewers)
$requests = [];
if ($role === 'student') {
    $stmt = $conn->prepare("
        SELECT r.*,
               rev.marks, rev.comment,
               p.name AS reviewer_name
        FROM requests r
        LEFT JOIN reviews  rev ON rev.request_id  = r.id
        LEFT JOIN profiles p   ON p.user_id        = rev.reviewer_id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$page_title = "Dashboard";
include __DIR__ . '/../includes/header.php';
?>

<div class="page-wrap">

  <!-- Welcome strip -->
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
    <div>
      <h1 class="page-title" style="margin-bottom:0;">Hi, <?= htmlspecialchars($profile['name']) ?> 👋</h1>
      <p class="page-subtitle" style="margin-bottom:0;"><?= htmlspecialchars($profile['branch']) ?> &middot; <span class="role-chip"><?= htmlspecialchars($role) ?></span></p>
    </div>
    <?php if ($role === 'student'): ?>
      <a href="request.php" class="btn btn-primary">+ Request Skill Test</a>
    <?php elseif (in_array($role, ['senior','faculty','hod'])): ?>
      <a href="reviewer_dashboard.php" class="btn btn-primary">Open Review Panel</a>
    <?php endif; ?>
  </div>

  <!-- Profile card -->
  <div class="card card-sm" style="margin-bottom:2rem;">
    <div class="flex-between">
      <span class="text-sm text-muted">Skills on profile</span>
    </div>
    <p style="margin-top:0.35rem;font-size:0.9rem;"><?= nl2br(htmlspecialchars($profile['skills'] ?: '—')) ?></p>
  </div>

  <?php if ($role === 'student'): ?>
  <!-- Student requests table -->
  <div class="card" style="padding:0;overflow:hidden;">
    <div class="card-header" style="padding:1.25rem 1.5rem;">
      <span class="card-title">My Skill Test Requests</span>
      <span class="text-sm text-muted"><?= count($requests) ?> total</span>
    </div>

    <?php if (empty($requests)): ?>
      <div class="empty-state">
        <div class="icon">📋</div>
        <h3>No requests yet</h3>
        <p>Submit your first skill test request to get reviewed.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Skill</th>
              <th>Status</th>
              <th>Review</th>
              <th>Requested</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($requests as $req): ?>
            <tr>
              <td><strong><?= htmlspecialchars($req['skill']) ?></strong></td>
              <td><span class="badge badge-<?= $req['status'] ?>"><?= $req['status'] ?></span></td>
              <td>
                <?php if ($req['status'] === 'completed'): ?>
                  <div class="review-block">
                    <div class="review-meta">By <?= htmlspecialchars($req['reviewer_name'] ?? 'Reviewer') ?></div>
                    <div class="marks"><?= (int)$req['marks'] ?><span> / 100</span></div>
                    <?php if ($req['comment']): ?>
                      <p style="font-size:0.85rem;color:var(--text-2);margin-top:0.4rem;"><?= htmlspecialchars($req['comment']) ?></p>
                    <?php endif; ?>
                  </div>
                <?php else: ?>
                  <span class="text-muted text-sm">—</span>
                <?php endif; ?>
              </td>
              <td class="text-muted text-sm"><?= date('d M Y', strtotime($req['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</div>
</body>
</html>
