<?php
require_once __DIR__ . '/../../app/auth.php';
require_login();
$u = current_user();

require_once __DIR__ . '/../../app/db.php';

$event_id = (int)$_POST['event_id'];
$team_id  = (int)$_POST['team_id'];

// Remove student from team
$stmt = db()->prepare("DELETE FROM team_members WHERE team_id=? AND user_id=?");
$stmt->execute([$team_id, $u['id']]);

header("Location: ../event_view.php?id=$event_id");
exit;