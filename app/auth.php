<?php
require_once __DIR__ . '/db.php';

function find_user_by_email($email) {
    $stmt = db()->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

function signup($name, $user_id,$role = 'student',$department,$year,$email, $password ) {
    // Hash the password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Ensure identifier is provided
    if (empty($user_id)) {
        throw new Exception("User ID (USN for students, Staff ID for staff) is required.");
    }

    // Insert into users table
    $stmt = db()->prepare("
        INSERT INTO users (name, user_id, role,department,year,email, password_hash) 
        VALUES (?,?,?,?,?,?,?)
    ");
    $stmt->execute([$name, $user_id, $role,$department,$year,$email, $hash]);

    return db()->lastInsertId();
}

function login($user_id, $password) {
    // Find user by identifier (USN or Staff ID)
    $stmt = db()->prepare("SELECT * FROM users WHERE user_id=?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // Verify password
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = [
            'id'             => $user['id'],
            'name'           => $user['name'],
            'user_id'        => $user['user_id'],   // USN or Staff ID
            'department'        => $user['department'],
            'year'        => $user['year'],
            'email'        => $user['email'],
            'role'           => $user['role']
        ];
        return true;
    }
    return false;
}
function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!current_user()) {
        header('Location: login.php');
        exit;
    }
}

function authorize($roles) {
    $u = current_user();
    if (!$u || !in_array($u['role'], $roles)) {
        http_response_code(403);
        echo "Forbidden";
        exit;
    }
}