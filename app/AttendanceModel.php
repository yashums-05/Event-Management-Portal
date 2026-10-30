<?php
require_once __DIR__.'/db.php';
class Attendance {
  public static function checkin(\,\){
    \=db()->prepare('INSERT INTO attendance (user_id,event_id,checkin_time) VALUES (?,?,NOW())');
    \->execute([\,\]);
  }
  public static function byEvent(\){
    \=db()->prepare('SELECT a.*, u.name FROM attendance a JOIN users u ON u.id=a.user_id WHERE event_id=? ORDER BY checkin_time ASC');
    \->execute([\]); return \->fetchAll();
  }
}
