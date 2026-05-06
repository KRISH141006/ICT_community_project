<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$allowed_roles = ['senior', 'faculty', 'hod'];
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: dashboard.php");
    exit;
}

$reviewer_id = (int) $_SESSION['user_id'];

// Accepted request currently open for review
$open_request_id = isset($_GET['accepted']) ? (int)$_GET['accepted'] : 0;

// Fetch pending requests
$pending = $conn->query("
    SELECT r.*, p.name AS student_name, p.branch
    FROM requests r
    JOIN profiles p ON p.user_id = r.user_id
    WHERE r.status = 'pending'
    ORDER BY r.created_at ASC
")->fetch_all(MYSQLI_ASSOC);

// Fetch accepted (open) requests — for review forms
$accepted = $conn->query("
    SELECT r.*, p.name AS student_name, p.branch
    FROM requests r
    JOIN profiles p ON p.user_id = r.user_id
    WHERE r.status = 'accepted'
    ORDER BY r.created_at ASC
")->fetch_all(MYSQLI_ASSOC);

// Fetch completed (this reviewer)
$completed = $conn->query("
    SELECT r.skill, r.created_at,
           p_s.name AS student_name,
           rev.marks, rev.comment, rev.created_at AS reviewed_at
    FROM reviews rev
    JOIN requests r   ON r.id        = rev.request_id
    JOIN profiles p_s ON p_s.user_id = r.user_id
    WHERE rev.reviewer_id = $reviewer_id
    ORDER BY rev.created_at DESC
    LIMIT 20
")->fetch_all(MYSQLI_ASSOC);

$page_title = "Reviewer Dashboard";
include __DIR__ . '/../includes/header.php';
?>

<div class="page-wrap">
  <h1 class="page-title">Review Panel</h1>
  <p class="page-subtitle">Manage pending skill test requests and submit reviews.</p>

  <!-- ── Pending Requests ── -->
  <div class="card" style="padding:0;overflow:hidden;margin-bottom:1.5rem;">
    <div class="card-header" style="padding:1.25rem 1.5rem;">
      <span class="card-title">Pending Requests</span>
      <span class="badge badge-pending"><?= count($pending) ?> pending</span>
    </div>

    <?php if (empty($pending)): ?>
      <div class="empty-state">
        <div class="icon">✅</div>
        <h3>All clear!</h3>
        <p>No pending skill test requests right now.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>Student</th><th>Branch</th><th>Skill</th><th>Requested</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php foreach ($pending as $req): ?>
            <tr>
              <td><strong><?= htmlspecialchars($req['student_name']) ?></strong></td>
              <td class="text-muted"><?= htmlspecialchars($req['branch']) ?></td>
              <td><?= htmlspecialchars($req['skill']) ?></td>
              <td class="text-muted text-sm"><?= date('d M Y', strtotime($req['created_at'])) ?></td>
              <td>
                <form action="../actions/accept_request.php" method="POST" style="display:inline;">
                  <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                  <button type="submit" class="btn btn-primary btn-sm">Accept</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- ── Accepted — ready to review ── -->
  <?php if (!empty($accepted)): ?>
  <div style="margin-bottom:1.5rem;">
    <h2 style="font-size:1rem;font-weight:600;color:var(--text);margin-bottom:0.75rem;">Accepted — Submit Reviews</h2>
    <?php foreach ($accepted as $req):
      $isOpen = ($open_request_id === (int)$req['id']);
    ?>
    <div class="card" style="margin-bottom:0.75rem;">
      <div class="flex-between" style="flex-wrap:wrap;gap:0.75rem;">
        <div>
          <strong><?= htmlspecialchars($req['student_name']) ?></strong>
          <span class="text-muted text-sm"> — <?= htmlspecialchars($req['skill']) ?></span>
          <div class="text-sm text-muted"><?= htmlspecialchars($req['branch']) ?></div>
        </div>
        <a href="?accepted=<?= $req['id'] ?>#review-<?= $req['id'] ?>" class="btn btn-success btn-sm">
          <?= $isOpen ? 'Reviewing…' : 'Write Review' ?>
        </a>
      </div>

      <?php if ($isOpen): ?>
      <div id="review-<?= $req['id'] ?>" style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border);">
        <form action="../actions/submit_review.php" method="POST">
          <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
          <div class="grid-2">
            <div class="form-group">
              <label>Marks <span class="text-muted">(0–100)</span></label>
              <input type="number" name="marks" min="0" max="100" placeholder="e.g. 78" required>
            </div>
          </div>
          <div class="form-group">
            <label>Comment / Feedback</label>
            <textarea name="comment" placeholder="Provide detailed feedback for the student…"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- ── Completed reviews by this reviewer ── -->
  <?php if (!empty($completed)): ?>
  <div class="card" style="padding:0;overflow:hidden;">
    <div class="card-header" style="padding:1.25rem 1.5rem;">
      <span class="card-title">My Past Reviews</span>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Student</th><th>Skill</th><th>Marks</th><th>Comment</th><th>Date</th></tr>
        </thead>
        <tbody>
          <?php foreach ($completed as $c): ?>
          <tr>
            <td><?= htmlspecialchars($c['student_name']) ?></td>
            <td><?= htmlspecialchars($c['skill']) ?></td>
            <td><strong style="color:var(--accent);"><?= $c['marks'] ?></strong><span class="text-muted text-sm"> /100</span></td>
            <td class="text-muted text-sm"><?= htmlspecialchars(mb_strimwidth($c['comment'], 0, 60, '…')) ?></td>
            <td class="text-muted text-sm"><?= date('d M Y', strtotime($c['reviewed_at'])) ?></td>
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
