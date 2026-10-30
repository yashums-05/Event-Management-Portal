<?php
require_once __DIR__ . '/config.php';

function db() {
    static $pdo;
    if (!$pdo) {
        $port = defined('DB_PORT') && DB_PORT ? ';port=' . DB_PORT : '';
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . $port . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    }
    return $pdo;
}