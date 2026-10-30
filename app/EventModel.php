<?php
require_once __DIR__ . '/db.php';

class EventModel {
    public static function all($q = null) {
        $sql = "SELECT e.*, u.name AS organizer_name 
                FROM events e 
                LEFT JOIN users u ON u.id = e.organizer_id 
                WHERE 1";
        $args = [];
        if ($q) {
            $sql .= " AND (e.title LIKE ? OR e.location LIKE ?)";
            $args = ["%$q%", "%$q%"];
        }
        $sql .= " ORDER BY start_time ASC";
        $stmt = db()->prepare($sql);
        $stmt->execute($args);
        return $stmt->fetchAll();
    }

    public static function create($d) {
        $stmt = db()->prepare(
            "INSERT INTO events (title, description, location, start_time, end_time, organizer_id, registration_type, team_min_size, team_max_size) 
             VALUES (?,?,?,?,?,?,?,?,?)"
        );
        $stmt->execute([
            $d['title'],
            $d['description'],
            $d['location'],
            $d['start_time'],
            $d['end_time'],
            $d['organizer_id'],
            $d['registration_type'],
            $d['team_min_size'] ?? 1,
            $d['team_max_size'] ?? 1
        ]);
        return db()->lastInsertId();
    }

    public static function find($id) {
        $stmt = db()->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function update($id, $d) {
        $stmt = db()->prepare(
            "UPDATE events 
             SET title=?, description=?, location=?, start_time=?, end_time=?, registration_type=?, team_min_size=?, team_max_size=? 
             WHERE id=?"
        );
        $stmt->execute([
            $d['title'],
            $d['description'],
            $d['location'],
            $d['start_time'],
            $d['end_time'],
            $d['registration_type'],
            $d['team_min_size'] ?? 1,
            $d['team_max_size'] ?? 1,
            $id
        ]);
    }

    public static function delete($id) {
        $stmt = db()->prepare("DELETE FROM events WHERE id=?");
        $stmt->execute([$id]);
    }
}