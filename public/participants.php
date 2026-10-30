<?php
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/EventModel.php';

$u = current_user();
$event_id = (int)($_GET['event_id'] ?? 0);
$event = EventModel::find($event_id);

if (!$event) {
    http_response_code(404);
    exit("Event not found");
}

$role = strtolower(trim($u['role']));
$can_manage = ($role === 'teacher' || ($role === 'organizer' && $u['id'] == $event['organizer_id']));

if (!$can_manage) {
    http_response_code(403);
    exit("Access Denied: Only teachers or the event organizer can view the participant roster.");
}

$department = $_GET['department'] ?? '';
$year = $_GET['year'] ?? '';

// Handle Individual registrations
if ($event['registration_type'] === 'individual') {
    $sql = "
        SELECT u.id AS user_pk, u.name, u.user_id, u.department, u.year, r.registered_at, r.removed_at, r.removed_role
        FROM registrations r
        JOIN users u ON u.id = r.user_id
        WHERE r.event_id = ?
    ";
    $params = [$event_id];

    if ($department !== '') {
        $sql .= " AND u.department = ?";
        $params[] = $department;
    }
    if ($year !== '') {
        $sql .= " AND u.year = ?";
        $params[] = $year;
    }

    $sql .= " ORDER BY r.registered_at DESC";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $participants = $stmt->fetchAll();
} else {
    // Team registrations
    $sql = "
        SELECT tm.id AS tm_id, t.team_name, u.id AS user_pk, u.user_id, u.name, u.email, u.department, u.year,
               tm.joined_at AS registered_at, tm.removed_by, tm.removed_role, tm.removed_at
        FROM teams t
        JOIN team_members tm ON tm.team_id = t.id
        JOIN users u ON u.id = tm.user_id
        WHERE t.event_id = ?
    ";
    $params = [$event_id];

    if ($department !== '') {
        $sql .= " AND u.department = ?";
        $params[] = $department;
    }
    if ($year !== '') {
        $sql .= " AND u.year = ?";
        $params[] = $year;
    }

    $sql .= " ORDER BY t.team_name ASC, tm.joined_at ASC";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $participants = $stmt->fetchAll();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Participants - <?= htmlspecialchars($event['title']) ?></title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../views/partials/nav.php'; ?>

<div class="container">
  <div class="page-header">
    <div>
      <a href="event_view.php?id=<?= $event_id ?>" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.9rem;">
        ← Back to Event
      </a>
      <h1 style="margin-top: 6px;">Participant Roster</h1>
      <p class="page-header-subtitle">
        Event: <strong><?= htmlspecialchars($event['title']) ?></strong> • 
        Format: <?= ucfirst($event['registration_type']) ?>
      </p>
    </div>

    <div style="display: flex; gap: 10px;">
      <button onclick="window.print()" class="btn btn-secondary btn-sm">🖨️ Print / Export</button>
    </div>
  </div>

  <!-- Filter Card -->
  <div class="filter-card">
    <form method="get">
      <input type="hidden" name="event_id" value="<?= $event_id ?>">

      <div class="filter-field">
        <label>Filter Department</label>
        <select name="department">
          <option value="">All Departments</option>
          <option value="CSE" <?= $department === 'CSE' ? 'selected' : '' ?>>CSE</option>
          <option value="ISE" <?= $department === 'ISE' ? 'selected' : '' ?>>ISE</option>
          <option value="ECE" <?= $department === 'ECE' ? 'selected' : '' ?>>ECE</option>
          <option value="EEE" <?= $department === 'EEE' ? 'selected' : '' ?>>EEE</option>
          <option value="ME" <?= $department === 'ME' ? 'selected' : '' ?>>Mechanical (ME)</option>
          <option value="CE" <?= $department === 'CE' ? 'selected' : '' ?>>Civil (CE)</option>
          <option value="AIML" <?= $department === 'AIML' ? 'selected' : '' ?>>AI & ML</option>
        </select>
      </div>

      <div class="filter-field">
        <label>Filter Year</label>
        <select name="year">
          <option value="">All Years</option>
          <option value="1" <?= $year === '1' ? 'selected' : '' ?>>1st Year</option>
          <option value="2" <?= $year === '2' ? 'selected' : '' ?>>2nd Year</option>
          <option value="3" <?= $year === '3' ? 'selected' : '' ?>>3rd Year</option>
          <option value="4" <?= $year === '4' ? 'selected' : '' ?>>4th Year</option>
        </select>
      </div>

      <button type="submit" class="btn btn-sm">Apply Filters</button>
      <?php if ($department || $year): ?>
        <a href="participants.php?event_id=<?= $event_id ?>" class="btn btn-secondary btn-sm">Reset</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Participants Table -->
  <div class="table-container">
    <?php if (empty($participants)): ?>
      <div style="padding: 40px; text-align: center; color: var(--text-secondary);">
        No participants match the selected filter criteria.
      </div>
    <?php else: ?>
      <table class="participants-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>USN / ID</th>
            <?php if ($event['registration_type'] === 'team'): ?>
              <th>Team Name</th>
            <?php endif; ?>
            <th>Department</th>
            <th>Year</th>
            <th>Registered At</th>
            <th>Status / Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $idx = 1; foreach ($participants as $p): ?>
            <tr>
              <td><?= $idx++ ?></td>
              <td style="font-weight: 600;"><?= htmlspecialchars($p['name']) ?></td>
              <td><code><?= htmlspecialchars($p['user_id']) ?></code></td>
              <?php if ($event['registration_type'] === 'team'): ?>
                <td style="font-weight: 600; color: #7c3aed;"><?= htmlspecialchars($p['team_name'] ?? '—') ?></td>
              <?php endif; ?>
              <td><?= htmlspecialchars($p['department'] ?? '—') ?></td>
              <td><?= !empty($p['year']) ? htmlspecialchars($p['year']) . ' Year' : '—' ?></td>
              <td><?= date('M j, Y • g:i A', strtotime($p['registered_at'])) ?></td>
              <td>
                <?php if (!empty($p['removed_at'])): ?>
                  <span style="color: var(--danger); font-size: 0.8rem; font-weight: 600;">
                    Removed (<?= htmlspecialchars($p['removed_role']) ?>)
                  </span>
                <?php else: ?>
                  <form method="post" action="remove_participant.php" style="display:inline;" onsubmit="return confirm('Remove this participant?');">
                    <input type="hidden" name="event_id" value="<?= $event_id ?>">
                    <input type="hidden" name="user_id" value="<?= $p['user_pk'] ?>">
                    <?php if (!empty($p['tm_id'])): ?>
                      <input type="hidden" name="team_member_id" value="<?= $p['tm_id'] ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn btn-sm btn-red" style="padding: 4px 10px; font-size: 0.75rem;">
                      Remove
                    </button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
