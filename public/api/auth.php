<?php
require_once __DIR__ . '/../../app/auth.php';
require_login();
authorize(['organizer']); // only organizers can create or edit events

require_once __DIR__ . '/../../app/EventModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = EventModel::create([
        'title'       => $_POST['title'],
        'description' => $_POST['description'],
        'location'    => $_POST['location'],
        'start_time'  => $_POST['start_time'],
        'end_time'    => $_POST['end_time'],
        'organizer_id'=> current_user()['id']
    ]);
    header('Location: ../event_view.php?id=' . $id);
    exit;
}