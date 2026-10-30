<?php
require_once __DIR__ . '/../app/auth.php';
require_login();

require_once __DIR__ . '/../app/EventModel.php';
require_once __DIR__ . '/../app/Registration.php';

$event_id = (int)($_GET['id'] ?? 0);
$event = EventModel::find($event_id);
if (!$event) {
    http_response_code(404);
    exit("Event not found");
}

$u = current_user();

// Check organizer details
$stmt = db()->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$event['organizer_id']]);
$organizer = $stmt->fetch();

$startTime = strtotime($event['start_time']);
$endTime = strtotime($event['end_time']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($event['title']) ?> - College Events</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../views/partials/nav.php'; ?>

<div class="container">
  <?php if (isset($_SESSION['flash'])): ?>
    <div class="flash-message">
      <span>✓</span>
      <span><?= htmlspecialchars($_SESSION['flash']) ?></span>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <div class="page-header">
    <div>
      <a href="events.php" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.9rem;">
        ← Back to all events
      </a>
      <h1 style="margin-top: 6px;"><?= htmlspecialchars($event['title']) ?></h1>
    </div>

    <!-- Management buttons for organizer or teacher -->
    <div style="display: flex; gap: 10px;">
      <?php if ($u['role'] === 'organizer' && $u['id'] == $event['organizer_id']): ?>
        <a href="dashboard.php?edit=<?= $event_id ?>" class="btn btn-secondary">✏️ Edit Event</a>
        <a href="participants.php?event_id=<?= $event_id ?>" class="btn">📋 View Participants</a>
      <?php elseif ($u['role'] === 'teacher'): ?>
        <a href="participants.php?event_id=<?= $event_id ?>" class="btn">📋 View Participants</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="event-view-grid">
    <!-- Left Main Column -->
    <div class="event-main-card">
      <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <span class="type-pill type-<?= $event['registration_type'] ?>">
          <?= ucfirst($event['registration_type']) ?> Event
        </span>
        <?php if ($event['registration_type'] === 'team'): ?>
          <span class="type-pill type-team">
            Team Size: <?= $event['team_min_size'] ?> to <?= $event['team_max_size'] ?> members
          </span>
        <?php endif; ?>
      </div>

      <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">
        About This Event
      </h3>
      <div style="font-size: 1rem; line-height: 1.7; color: var(--text-secondary); white-space: pre-line;">
        <?= htmlspecialchars($event['description']) ?>
      </div>
    </div>

    <!-- Right Sidebar Column -->
    <div class="event-sidebar-card">
      <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 16px;">Schedule & Details</h3>

      <div class="info-item">
        <div class="info-icon">📍</div>
        <div class="info-content">
          <span class="info-label">Venue / Location</span>
          <span class="info-value"><?= htmlspecialchars($event['location']) ?></span>
        </div>
      </div>

      <div class="info-item">
        <div class="info-icon">📅</div>
        <div class="info-content">
          <span class="info-label">Starts</span>
          <span class="info-value"><?= date('D, M j, Y @ g:i A', $startTime) ?></span>
        </div>
      </div>

      <div class="info-item">
        <div class="info-icon">🏁</div>
        <div class="info-content">
          <span class="info-label">Ends</span>
          <span class="info-value"><?= date('D, M j, Y @ g:i A', $endTime) ?></span>
        </div>
      </div>

      <div class="info-item">
        <div class="info-icon">🎟️</div>
        <div class="info-content">
          <span class="info-label">Format</span>
          <span class="info-value"><?= ucfirst($event['registration_type']) ?></span>
        </div>
      </div>

      <div style="margin-top: 24px;">
        <!-- Registration Actions for Student -->
        <?php if ($u['role'] === 'student'): ?>
          <?php if ($event['registration_type'] === 'individual'): ?>
            <?php if (!Registration::isRegistered($u['id'], $event_id)): ?>
              <form method="post" action="api/register.php">
                <input type="hidden" name="event_id" value="<?= $event_id ?>">
                <button type="submit" class="btn btn-lg" style="width: 100%;">
                  Register for Event
                </button>
              </form>
            <?php else: ?>
              <div style="background: var(--success-bg); border: 1px solid var(--success-border); padding: 14px; border-radius: var(--radius-md); text-align: center; margin-bottom: 12px;">
                <div style="color: #065f46; font-weight: 700; font-size: 0.95rem;">✓ You are Registered</div>
                <div style="font-size: 0.8rem; color: #047857; margin-top: 2px;">Your spot is reserved.</div>
              </div>
              <form method="post" action="api/unregister.php">
                <input type="hidden" name="event_id" value="<?= $event_id ?>">
                <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;" onclick="return confirm('Cancel registration for this event?');">
                  Cancel Registration
                </button>
              </form>
            <?php endif; ?>
          <?php else: ?>
            <!-- Team Event Logic -->
            <?php
            $stmt = db()->prepare("
              SELECT t.id AS team_id, t.team_name
              FROM teams t
              JOIN team_members tm ON tm.team_id = t.id
              WHERE t.event_id = ? AND tm.user_id = ?
            ");
            $stmt->execute([$event_id, $u['id']]);
            $team = $stmt->fetch();
            ?>

            <?php if (!$team): ?>
              <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 12px;">Register Your Team</h4>
              <form method="post" action="api/team_register.php">
                <input type="hidden" name="event_id" value="<?= $event_id ?>">
                
                <div class="form-group">
                  <label>Team Name</label>
                  <input type="text" name="team_name" placeholder="e.g. CodeWarriors" required>
                </div>

                <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 10px;">
                  You are the team captain (<?= htmlspecialchars($u['user_id']) ?>). Enter teammates' USNs below:
                </div>

                <?php
                $min = (int)$event['team_min_size'];
                $max = (int)$event['team_max_size'];
                for ($i = 1; $i <= $max - 1; $i++):
                    $required = $i <= ($min - 1);
                ?>
                  <div class="form-group">
                    <label>Teammate <?= $i ?> USN <?= $required ? '<span style="color: var(--danger)">*</span>' : '(Optional)' ?></label>
                    <input type="text" name="members[]" placeholder="Enter teammate USN" <?= $required ? 'required' : '' ?>>
                  </div>
                <?php endfor; ?>

                <button type="submit" class="btn btn-lg" style="width: 100%; margin-top: 6px;">
                  Register Team
                </button>
              </form>
            <?php else: ?>
              <div style="background: var(--success-bg); border: 1px solid var(--success-border); padding: 16px; border-radius: var(--radius-md); text-align: center; margin-bottom: 14px;">
                <div style="color: #065f46; font-weight: 700; font-size: 1rem;">Team Registered</div>
                <div style="font-size: 0.9rem; color: #047857; margin-top: 4px;">
                  Team: <strong><?= htmlspecialchars($team['team_name']) ?></strong>
                </div>
              </div>

              <form method="post" action="api/team_unregister.php">
                <input type="hidden" name="event_id" value="<?= $event_id ?>">
                <input type="hidden" name="team_id" value="<?= $team['team_id'] ?>">
                <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%;" onclick="return confirm('Leave this team?');">
                  Leave Team
                </button>
              </form>
            <?php endif; ?>
          <?php endif; ?>
        <?php else: ?>
          <div style="background: #f8fafc; border: 1px solid var(--border); padding: 14px; border-radius: var(--radius-md); text-align: center; font-size: 0.88rem; color: var(--text-secondary);">
            Logged in as <strong><?= ucfirst($u['role']) ?></strong>.<br>
            Student role required to register.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
