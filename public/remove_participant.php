<?php
require_once __DIR__ . '/../app/auth.php';
require_login();
$u = current_user();

if (!in_array($u['role'], ['teacher','organizer'])) {
    http_response_code(403);
    exit("Not allowed");
}

require_once __DIR__ . '/../app/db.php';

$event_id = (int)$_POST['event_id'];
$user_id  = (int)$_POST['user_id'];
$tm_id    = $_POST['team_member_id'] ?? null;

if ($tm_id) {
    $stmt = db()->prepare("UPDATE team_members SET removed_by=?, removed_role=?, removed_at=NOW() WHERE id=?");
    $stmt->execute([$u['id'], $u['role'], $tm_id]);
} else {
    $stmt = db()->prepare("UPDATE registrations SET removed_by=?, removed_role=?, removed_at=NOW() WHERE user_id=? AND event_id=?");
    $stmt->execute([$u['id'], $u['role'], $user_id, $event_id]);
}

header("Location: participants.php?event_id=$event_id");
exit;