<?php
require_once __DIR__ . '/db.php';

class Registration {
    public static function add($user_id, $event_id) {
        $stmt = db()->prepare("INSERT IGNORE INTO registrations (user_id, event_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $event_id]);
    }

    public static function remove($user_id, $event_id) {
        $stmt = db()->prepare("DELETE FROM registrations WHERE user_id = ? AND event_id = ?");
        $stmt->execute([$user_id, $event_id]);
    }

    public static function listByEvent($event_id) {
        $stmt = db()->prepare(
            "SELECT r.*, u.name 
             FROM registrations r 
             JOIN users u ON u.id = r.user_id 
             WHERE r.event_id = ?"
        );
        $stmt->execute([$event_id]);
        return $stmt->fetchAll();
    }

    public static function isRegistered($user_id, $event_id) {
        $stmt = db()->prepare("SELECT 1 FROM registrations WHERE user_id = ? AND event_id = ? LIMIT 1");
        $stmt->execute([$user_id, $event_id]);
        return (bool) $stmt->fetchColumn();
    }
}