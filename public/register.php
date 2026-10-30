<?php
require_once __DIR__ . '/../app/auth.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        signup(
            $_POST['name'],
            $_POST['user_id'],
            $_POST['role'],
            $_POST['department'],
            $_POST['year'] ?? null,
            $_POST['email'],
            $_POST['password']
        );
        header('Location: login.php?registered=1');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account - College Events</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <script>
    function toggleYearField() {
      const role = document.getElementById('role').value;
      const yearField = document.getElementById('year-field');
      const yearSelect = yearField.querySelector('select');

      if (role === 'teacher' || role === 'organizer') {
        yearField.style.display = 'none';
        yearSelect.removeAttribute('required');
      } else {
        yearField.style.display = 'block';
        yearSelect.setAttribute('required', 'required');
      }
    }

    document.addEventListener("DOMContentLoaded", toggleYearField);
  </script>
</head>
<body>
  <?php include __DIR__ . '/../views/partials/nav.php'; ?>

  <div class="auth-wrapper">
    <div class="auth-card" style="max-width: 480px;">
      <div class="auth-header">
        <div class="logo-badge">🚀</div>
        <h1>Create Account</h1>
        <p>Join the portal to register for events or organize them</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="flash error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="name" placeholder="John Doe" required autofocus>
        </div>

        <div class="form-group">
          <label>USN / Staff ID</label>
          <input type="text" name="user_id" placeholder="Enter USN or Staff ID" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div class="form-group">
            <label>Role</label>
            <select name="role" id="role" required onchange="toggleYearField()">
              <option value="student">Student</option>
              <option value="organizer">Organizer</option>
              <option value="teacher">Teacher</option>
            </select>
          </div>

          <div class="form-group">
            <label>Department</label>
            <select name="department" required>
              <option value="CSE">CSE</option>
              <option value="ISE">ISE</option>
              <option value="ECE">ECE</option>
              <option value="EEE">EEE</option>
              <option value="ME">Mechanical</option>
              <option value="CE">Civil</option>
              <option value="AIML">AI & ML</option>
            </select>
          </div>
        </div>

        <div class="form-group" id="year-field">
          <label>Academic Year</label>
          <select name="year">
            <option value="1">1st Year</option>
            <option value="2">2nd Year</option>
            <option value="3">3rd Year</option>
            <option value="4">4th Year</option>
          </select>
        </div>

        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="student@college.edu" required>
        </div>

        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Create a strong password" required minlength="4">
        </div>

        <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">Create Account</button>
      </form>

      <div class="auth-footer">
        Already registered? <a href="login.php">Sign In</a>
      </div>
    </div>
  </div>
</body>
</html>
