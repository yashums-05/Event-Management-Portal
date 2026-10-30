<?php
require_once __DIR__ . '/../../app/auth.php';
require_login();
require_once __DIR__ . '/../../app/db.php';

$u = current_user();
$event_id = (int)$_POST['event_id'];
$team_name = trim($_POST['team_name']);
$members = $_POST['members'] ?? [];

// Fetch event to check min/max
$stmt = db()->prepare("SELECT team_min_size, team_max_size FROM events WHERE id=?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    echo "<script>alert('Event not found'); window.location='../event_view.php?id=$event_id';</script>";
    exit;
}

$min = (int)$event['team_min_size'];
$max = (int)$event['team_max_size'];

// Filter out empty identifiers
$validMembers = array_filter($members, fn($m) => !empty(trim($m)));

// Validate team size (including current user)
$totalSize = 1 + count($validMembers);
if ($totalSize < $min) {
    echo "<script>alert('You must provide at least $min members including yourself.'); window.location='../event_view.php?id=$event_id';</script>";
    exit;
}
if ($totalSize > $max) {
    echo "<script>alert('Team cannot exceed $max members.'); window.location='../event_view.php?id=$event_id';</script>";
    exit;
}

// ✅ Validate all members BEFORE creating team
foreach ($validMembers as $identifier) {
    $identifier = trim($identifier);

    $stmt = db()->prepare("SELECT id, role FROM users WHERE user_id=?");
    $stmt->execute([$identifier]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "<script>alert('User ID $identifier does not exist in the portal. All team members must be registered users.'); window.location='../event_view.php?id=$event_id';</script>";
        exit;
    }
    if ($user['role'] !== 'student') {
        echo "<script>alert('User ID $identifier belongs to a {$user['role']} and cannot join a team.'); window.location='../event_view.php?id=$event_id';</script>";
        exit;
    }
}

// ✅ If we reach here, all members are valid → create team
$stmt = db()->prepare("INSERT INTO teams (event_id, team_name) VALUES (?, ?)");
$stmt->execute([$event_id, $team_name]);
$team_id = db()->lastInsertId();

// Add current user
$stmt = db()->prepare("INSERT INTO team_members (team_id, user_id) VALUES (?, ?)");
$stmt->execute([$team_id, $u['id']]);

// Add other members
foreach ($validMembers as $identifier) {
    $stmt = db()->prepare("SELECT id FROM users WHERE user_id=?");
    $stmt->execute([trim($identifier)]);
    $user = $stmt->fetch();
    $stmt = db()->prepare("INSERT IGNORE INTO team_members (team_id, user_id) VALUES (?, ?)");
    $stmt->execute([$team_id, $user['id']]);
}

// Success popup
echo "<script>alert('Team registered successfully!'); window.location='../event_view.php?id=$event_id';</script>";
exit;