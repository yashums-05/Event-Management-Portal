<?php
require_once __DIR__ . '/../app/auth.php';
require_login();
authorize(['organizer']);

require_once __DIR__ . '/../app/EventModel.php';

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$event = $editId ? EventModel::find($editId) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        EventModel::delete($editId);
        header('Location: events.php');
        exit;
    } elseif ($editId) {
        EventModel::update($editId, $_POST);
        header('Location: event_view.php?id=' . $editId);
        exit;
    } else {
        $id = EventModel::create([
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'location' => $_POST['location'],
            'start_time' => $_POST['start_time'],
            'end_time' => $_POST['end_time'],
            'organizer_id' => current_user()['id'],
            'registration_type' => $_POST['registration_type'],
            'team_min_size' => $_POST['team_min_size'] ?? 1,
            'team_max_size' => $_POST['team_max_size'] ?? 1
        ]);
        header('Location: event_view.php?id=' . $id);
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $editId ? 'Edit Event' : 'Create New Event' ?> - College Events</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <script>
    function toggleTeamSize(val) {
      document.getElementById('teamSizeFields').style.display = (val === 'team') ? 'grid' : 'none';
    }
  </script>
</head>
<body>
<?php include __DIR__ . '/../views/partials/nav.php'; ?>

<div class="container">
  <div class="page-header" style="max-width: 680px; margin: 0 auto 28px;">
    <div>
      <a href="events.php" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.9rem;">
        ← Back to events
      </a>
      <h1 style="margin-top: 6px;"><?= $editId ? 'Edit Event' : 'Create New Event' ?></h1>
      <p class="page-header-subtitle">Fill in the details to publish your campus event</p>
    </div>
  </div>

  <div class="form-card" style="max-width: 680px;">
    <form method="post">
      <div class="form-group">
        <label>Event Title</label>
        <input name="title" value="<?= htmlspecialchars($event['title'] ?? '') ?>" placeholder="e.g. Annual Hackathon 2026" required>
      </div>

      <div class="form-group">
        <label>Event Description & Rules</label>
        <textarea name="description" placeholder="Provide event schedule, rules, eligibility, prizes, etc." required><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
      </div>

      <div class="form-group">
        <label>Venue / Location</label>
        <input name="location" value="<?= htmlspecialchars($event['location'] ?? '') ?>" placeholder="e.g. Main Auditorium / CSE Seminar Hall" required>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label>Start Date & Time</label>
          <input type="datetime-local" name="start_time"
                 value="<?= $event ? date('Y-m-d\TH:i', strtotime($event['start_time'])) : '' ?>" required>
        </div>

        <div class="form-group">
          <label>End Date & Time</label>
          <input type="datetime-local" name="end_time"
                 value="<?= $event ? date('Y-m-d\TH:i', strtotime($event['end_time'])) : '' ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label>Registration Type</label>
        <select name="registration_type" id="regType" required onchange="toggleTeamSize(this.value)">
          <option value="individual" <?= ($event['registration_type'] ?? '') === 'individual' ? 'selected' : '' ?>>
            Individual (Solo Participation)
          </option>
          <option value="team" <?= ($event['registration_type'] ?? '') === 'team' ? 'selected' : '' ?>>
            Team Event (Group Participation)
          </option>
        </select>
      </div>

      <div id="teamSizeFields" style="display: <?= ($event['registration_type'] ?? '') === 'team' ? 'grid' : 'none' ?>; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label>Minimum Team Size</label>
          <input type="number" name="team_min_size" min="2" max="20"
                 value="<?= htmlspecialchars($event['team_min_size'] ?? 2) ?>">
        </div>

        <div class="form-group">
          <label>Maximum Team Size</label>
          <input type="number" name="team_max_size" min="2" max="20"
                 value="<?= htmlspecialchars($event['team_max_size'] ?? 4) ?>">
        </div>
      </div>

      <div class="form-actions" style="justify-content: space-between;">
        <button type="submit" class="btn btn-lg">
          <?= $editId ? 'Save Changes' : 'Publish Event' ?>
        </button>

        <?php if ($editId): ?>
          <button type="submit" name="delete" value="1" class="btn btn-red"
                  onclick="return confirm('Deleting this event will remove all registrations. Are you sure?');">
            Delete Event
          </button>
        <?php endif; ?>
      </div>
    </form>
  </div>
</div>
</body>
</html>
