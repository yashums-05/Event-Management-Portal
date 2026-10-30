<?php
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/EventModel.php';
$u = current_user();
$recent_events = EventModel::all();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MCE Hassan Events Portal - Discover & Participate</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../views/partials/nav.php'; ?>

<div class="container">
  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-pill">🏛️ Malnad College of Engineering, Hassan</div>
    <h1>Discover, Organize & Participate in <span>MCE Campus Events</span></h1>
    <p>Official student registration portal for MCE Hassan's flagship fests — Aavishkar Manthan, ENIGMA, The Malnad Fest, hackathons, and student club activities.</p>
    
    <div class="hero-actions">
      <a class="btn btn-lg" href="events.php">Explore All Events →</a>
      <?php if (!$u): ?>
        <a class="btn btn-lg btn-secondary" href="register.php">Create Free Account</a>
      <?php elseif ($u['role'] === 'organizer'): ?>
        <a class="btn btn-lg btn-outline" href="dashboard.php">+ Create New Event</a>
      <?php endif; ?>
    </div>
  </section>

  <!-- Features Grid -->
  <section class="features-grid">
    <div class="feature-card">
      <div class="feature-icon blue">⚡</div>
      <h3>Instant Registration</h3>
      <p>Seamless signups for individual contests and team events with USN validation across all MCE branches.</p>
    </div>

    <div class="feature-card">
      <div class="feature-icon purple">🎯</div>
      <h3>Student Club Portals</h3>
      <p>Direct event management for IUCEE-EWB, Malnad Technical Club, The Literary Club, and SoUL chapters.</p>
    </div>

    <div class="feature-card">
      <div class="feature-icon emerald">📊</div>
      <h3>Teacher & Organizer Review</h3>
      <p>Faculty advisors and club coordinators can monitor participant rosters filtered by department and academic year.</p>
    </div>
  </section>

  <!-- Campus Events Grid -->
  <div style="margin-top: 60px;">
    <div class="page-header">
      <div>
        <h2 style="font-size: 1.6rem; font-weight: 800;">Featured Campus Events</h2>
        <p class="page-header-subtitle">Stay tuned with competitions, workshops, and fests at MCE Hassan</p>
      </div>
      <a href="events.php" class="btn btn-outline btn-sm">View All (<?= count($recent_events) ?> Events) →</a>
    </div>

    <div class="grid">
      <?php if (empty($recent_events)): ?>
        <div class="empty-state">
          <div class="empty-state-icon">🎪</div>
          <h3>No events right now</h3>
          <p>The slate is currently clean. Check back soon for announcements!</p>
        </div>
      <?php else: ?>
        <?php foreach ($recent_events as $e): ?>
          <?php
            $time = strtotime($e['start_time']);
            $month = date('M', $time);
            $day = date('d', $time);
            $formatted_date = date('D, M j, Y • g:i A', $time);
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
                  <span>🕒</span>
                  <span><?= $formatted_date ?></span>
                </div>
              </div>

              <p class="card-description">
                <?= nl2br(htmlspecialchars(substr($e['description'], 0, 140))) ?>...
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
</div>
</body>
</html>
