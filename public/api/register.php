<?php
require_once __DIR__ . '/../../app/auth.php';
require_login();
require_once __DIR__ . '/../../app/db.php';

session_start();
$u = current_user();
$event_id = (int)$_POST['event_id'];

if ($u['role'] !== 'student') {
    $_SESSION['flash'] = "Only students can register for events.";
    header("Location: ../event_view.php?id=$event_id");
    exit;
}

$stmt = db()->prepare("SELECT id FROM registrations WHERE user_id=? AND event_id=?");
$stmt->execute([$u['id'], $event_id]);
if ($stmt->fetch()) {
    $_SESSION['flash'] = "You are already registered for this event.";
    header("Location: ../event_view.php?id=$event_id");
    exit;
}

$stmt = db()->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?, ?)");
$stmt->execute([$u['id'], $event_id]);

$_SESSION['flash'] = "Successfully registered for the event!";
header("Location: ../event_view.php?id=$event_id");
exit;