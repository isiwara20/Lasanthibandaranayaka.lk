<?php
require_once __DIR__ . '/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Lasanthi Bandaranayaka Salon & Academy</title>
  <link rel="icon" type="image/png" href="../assets/images/logo_transparent.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/admin.css">
  <style>
    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: radial-gradient(circle at center, #1b1611 0%, #080605 100%);
      padding: 20px;
    }
    .login-box {
      width: 100%;
      max-width: 420px;
      background: #14100c;
      border: 1px solid rgba(212, 175, 55, 0.3);
      border-radius: 20px;
      padding: 40px 32px;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8), 0 0 30px rgba(212, 175, 55, 0.15);
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <img src="../assets/images/logo_transparent.png" alt="Lasanthi Salon Logo" style="height: 75px; width: auto; margin-bottom: 16px;">
      <h1 style="font-family: var(--font-serif); font-size: 1.6rem; color: #fff; margin-bottom: 6px;">Admin Portal</h1>
      <p style="color: var(--admin-muted); font-size: 0.86rem; margin-bottom: 26px;">Lasanthi Bandaranayaka Salon & Academy</p>

      <?php if (!empty($error)): ?>
        <div class="alert-box alert-error" style="text-align: left; margin-bottom: 20px;">
          <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php" style="text-align: left;">
        <div class="admin-form-group">
          <label class="admin-label" for="username"><i class="fa-solid fa-user"></i> Username</label>
          <input type="text" id="username" name="username" class="admin-input" placeholder="Enter username" required autofocus>
        </div>

        <div class="admin-form-group">
          <label class="admin-label" for="password"><i class="fa-solid fa-lock"></i> Password</label>
          <input type="password" id="password" name="password" class="admin-input" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-admin btn-admin-gold" style="width: 100%; justify-content: center; padding: 13px; font-size: 0.95rem; margin-top: 10px;">
          <i class="fa-solid fa-right-to-bracket"></i>
          <span>Sign In to Dashboard</span>
        </button>
      </form>
    </div>
  </div>
</body>
</html>
