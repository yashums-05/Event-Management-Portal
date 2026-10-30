<?php
require_once __DIR__ . '/../app/auth.php';

if (isset($_GET['logout'])) {
    unset($_SESSION['user']);
    session_destroy();
    header('Location: login.php');
    exit;
}

if (current_user()) {
    header('Location: events.php');
    exit;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (login($_POST['user_id'], $_POST['password'])) {
        header('Location: events.php');
        exit;
    }
    $error = "Invalid USN / Staff ID or password";
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - College Events Portal</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <?php include __DIR__ . '/../views/partials/nav.php'; ?>

  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-header">
        <div class="logo-badge">🎓</div>
        <h1>Welcome Back</h1>
        <p>Sign in to access campus events and registrations</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="flash error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="form-group">
          <label>USN or Staff ID</label>
          <input type="text" name="user_id" placeholder="Enter USN or Staff ID" required autofocus>
        </div>

        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">Sign In</button>
      </form>

      <div class="auth-footer">
        Don't have an account yet? <a href="register.php">Create an account</a>
      </div>
    </div>
  </div>
</body>
</html>
