<?php
require_once __DIR__ . '/../app/auth.php';
require_login();
require_once __DIR__ . '/../app/EventModel.php';

$q = $_GET['q'] ?? null;
$type_filter = $_GET['type'] ?? 'all';

$events = EventModel::all($q);

// Apply type filter if set
if ($type_filter && $type_filter !== 'all') {
    $events = array_filter($events, function($e) use ($type_filter) {
        return ($e['registration_type'] ?? '') === $type_filter;
    });
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Events - College Events Portal</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <script defer src="../assets/js/app.js"></script>
</head>
<body>
<?php include __DIR__ . '/../views/partials/nav.php'; ?>

<div class="container">
  <div class="page-header">
    <div>
      <h1>Upcoming Events</h1>
      <p class="page-header-subtitle">Explore and register for hackathons, workshops, and campus activities</p>
    </div>

    <form method="get" class="search-form">
      <?php if ($type_filter !== 'all'): ?>
        <input type="hidden" name="type" value="<?= htmlspecialchars($type_filter) ?>">
      <?php endif; ?>
      <input name="q" value="<?= htmlspecialchars($q ?? '') ?>" placeholder="Search by title, location...">
      <button class="btn btn-sm" type="submit">Search</button>
    </form>
  </div>

  <!-- Filter chips -->
  <div style="display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap;">
    <a href="events.php<?= $q ? '?q=' . urlencode($q) : '' ?>" 
       class="btn btn-sm <?= $type_filter === 'all' ? '' : 'btn-secondary' ?>">
       All Events
    </a>
    <a href="events.php?type=individual<?= $q ? '&q=' . urlencode($q) : '' ?>" 
       class="btn btn-sm <?= $type_filter === 'individual' ? '' : 'btn-secondary' ?>">
       👤 Individual
    </a>
    <a href="events.php?type=team<?= $q ? '&q=' . urlencode($q) : '' ?>" 
       class="btn btn-sm <?= $type_filter === 'team' ? '' : 'btn-secondary' ?>">
       👥 Team Events
    </a>
  </div>

  <div class="grid">
    <?php if (empty($events)): ?>
      <div class="empty-state">
        <div class="empty-state-icon">🔍</div>
        <h3>No events found</h3>
        <p><?= $q ? "No events matching '".htmlspecialchars($q)."'." : "There are currently no events posted." ?></p>
        <?php if ($q || $type_filter !== 'all'): ?>
          <a href="events.php" class="btn btn-secondary">Clear Filters</a>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <?php foreach ($events as $e): ?>
        <?php
          $startTime = strtotime($e['start_time']);
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
              <span class="type-pill type-<?= $e['registration_type'] ?>">
                <?= ucfirst($e['registration_type']) ?>
                <?= $e['registration_type'] === 'team' ? "({$e['team_min_size']}-{$e['team_max_size']} members)" : '' ?>
              </span>
            </div>

            <h3><?= htmlspecialchars($e['title']) ?></h3>

            <div class="card-meta">
              <div class="card-meta-item">
                <span>📍</span>
                <span><?= htmlspecialchars($e['location']) ?></span>
              </div>
              <div class="card-meta-item">
                <span>⏰</span>
                <span><?= $timeStr ?></span>
              </div>
            </div>

            <p class="card-description">
              <?= nl2br(htmlspecialchars(substr($e['description'], 0, 150))) ?><?= strlen($e['description']) > 150 ? '...' : '' ?>
            </p>
          </div>

          <div class="actions" style="justify-content: flex-end;">
            <a class="btn btn-sm" href="event_view.php?id=<?= $e['id'] ?>">View Details →</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
