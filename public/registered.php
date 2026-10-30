<?php
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/db.php';

$u = current_user();

// If event_id is passed and user is teacher/organizer, forward to participants.php
$event_id = (int)($_GET['event_id'] ?? 0);
if ($event_id > 0 && in_array($u['role'], ['teacher', 'organizer'])) {
    header("Location: participants.php?event_id=$event_id");
    exit;
}

// Fetch individual registrations for current student
$stmtInd = db()->prepare("
    SELECT e.*, r.registered_at, 'individual' AS participation_type, NULL AS team_name
    FROM registrations r
    JOIN events e ON e.id = r.event_id
    WHERE r.user_id = ?
    ORDER BY e.start_time ASC
");
$stmtInd->execute([$u['id']]);
$indRegistrations = $stmtInd->fetchAll();

// Fetch team registrations for current student
$stmtTeam = db()->prepare("
    SELECT e.*, tm.joined_at AS registered_at, 'team' AS participation_type, t.team_name, t.id AS team_id
    FROM team_members tm
    JOIN teams t ON t.id = tm.team_id
    JOIN events e ON e.id = t.event_id
    WHERE tm.user_id = ?
    ORDER BY e.start_time ASC
");
$stmtTeam->execute([$u['id']]);
$teamRegistrations = $stmtTeam->fetchAll();

// Merge and sort by start_time
$allRegistrations = array_merge($indRegistrations, $teamRegistrations);
usort($allRegistrations, function($a, $b) {
    return strtotime($a['start_time']) - strtotime($b['start_time']);
});
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Registrations - College Events</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../views/partials/nav.php'; ?>

<div class="container">
  <div class="page-header">
    <div>
      <h1>My Registered Events</h1>
      <p class="page-header-subtitle">Track your upcoming activities, individual sign-ups, and team participation</p>
    </div>
    <a href="events.php" class="btn btn-outline btn-sm">+ Register for More Events</a>
  </div>

  <?php if (empty($allRegistrations)): ?>
    <div class="empty-state">
      <div class="empty-state-icon">🎟️</div>
      <h3>No registrations yet</h3>
      <p>You haven't signed up for any events yet. Check out the active events calendar and register today!</p>
      <a href="events.php" class="btn">Explore Events →</a>
    </div>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($allRegistrations as $item): ?>
        <?php
          $startTime = strtotime($item['start_time']);
          $month = date('M', $startTime);
          $day = date('d', $startTime);
          $timeStr = date('D, M j, Y • g:i A', $startTime);
        ?>
        <div class="card">
          <div>
            <div class="card-top-bar">
              <div class="date-badge">
                <span class="month"><?= $month ?></span>
                <span class="day"><?= $day ?></span>
              </div>
              <div>
                <span class="type-pill type-<?= $item['participation_type'] ?>">
                  <?= ucfirst($item['participation_type']) ?>
                </span>
              </div>
            </div>

            <h3><?= htmlspecialchars($item['title']) ?></h3>

            <?php if (!empty($item['team_name'])): ?>
              <div style="font-size: 0.9rem; font-weight: 600; color: #7c3aed; margin-bottom: 8px;">
                👥 Team: <?= htmlspecialchars($item['team_name']) ?>
              </div>
            <?php endif; ?>

            <div class="card-meta">
              <div class="card-meta-item">
                <span>📍</span>
                <span><?= htmlspecialchars($item['location']) ?></span>
              </div>
              <div class="card-meta-item">
                <span>⏰</span>
                <span><?= $timeStr ?></span>
              </div>
              <div class="card-meta-item" style="color: var(--success); font-weight: 600;">
                <span>✓</span>
                <span>Registered on <?= date('M j, Y', strtotime($item['registered_at'])) ?></span>
              </div>
            </div>
          </div>

          <div class="actions">
            <a href="event_view.php?id=<?= $item['id'] ?>" class="btn btn-sm">
              View Event Page →
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
