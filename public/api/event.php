<?php
require_once __DIR__ . '/../../app/auth.php';
require_login();

require_once __DIR__ . '/../../app/Registration.php';

$u = current_user();
$action = $_GET['action'] ?? '';
$event_id = isset($_POST['event_id']) ? (int) $_POST['event_id'] : 0;

if (!$event_id) { http_response_code(400); exit('Missing event_id'); }

if ($action === 'register') {
    if ($u['role'] !== 'student') { http_response_code(403); exit; }
    Registration::add($u['id'], $event_id);
    echo json_encode(['ok' => true]); exit;
}

if ($action === 'unregister') {
    if ($u['role'] !== 'student') { http_response_code(403); exit; }
    Registration::remove($u['id'], $event_id);
    echo json_encode(['ok' => true]); exit;
}

if ($action === 'checkin') {
    if ($u['role'] !== 'student') { http_response_code(403); exit; }
    // implement Attendance::checkin($u['id'], $event_id);
    echo json_encode(['ok' => true]); exit;
}

http_response_code(404);