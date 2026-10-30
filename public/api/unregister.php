<?php
require_once __DIR__ . '/../../app/auth.php';
require_login();
$u = current_user();

require_once __DIR__ . '/../../app/db.php';

$event_id = (int)$_POST['event_id'];

$stmt = db()->prepare("DELETE FROM registrations WHERE user_id=? AND event_id=?");
$stmt->execute([$u['id'], $event_id]);

header("Location: ../event_view.php?id=$event_id");
exit;