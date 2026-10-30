<?php
$u = current_user();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
  <div class="container">
    <!-- Brand -->
    <a href="index.php" class="brand">
      <span class="brand-icon">🎓</span>
      <span>CollegeEvents</span>
    </a>

    <!-- Navigation links -->
    <div class="nav-links">
      <a href="events.php" class="<?= $current_page === 'events.php' ? 'active' : '' ?>">Browse Events</a>
      <?php if ($u && $u['role'] === 'student'): ?>
        <a href="registered.php" class="<?= $current_page === 'registered.php' ? 'active' : '' ?>">My Registrations</a>
      <?php endif; ?>
      <?php if ($u && $u['role'] === 'organizer'): ?>
        <a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>">+ Create Event</a>
      <?php endif; ?>
    </div>

    <!-- User info / Auth links -->
    <div class="nav-user">
      <?php if ($u): ?>
        <div class="user-badge">
          <span><?= htmlspecialchars($u['name'] ?? 'User') ?></span>
          <span class="role-pill role-<?= htmlspecialchars(strtolower($u['role'] ?? 'student')) ?>">
            <?= htmlspecialchars(ucfirst($u['role'] ?? '')) ?>
          </span>
        </div>
        <a href="login.php?logout=1" class="btn-logout">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-secondary btn-sm">Login</a>
        <a href="register.php" class="btn btn-sm">Sign Up</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
