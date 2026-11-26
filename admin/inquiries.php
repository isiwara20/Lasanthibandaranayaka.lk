<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

$dataFile = __DIR__ . '/../data/inquiries.json';
$inquiries = json_decode(@file_get_contents($dataFile), true) ?: [];

$message = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $delId = $_GET['delete'];
    $inquiries = array_values(array_filter($inquiries, fn($i) => $i['id'] !== $delId));
    file_put_contents($dataFile, json_encode($inquiries, JSON_PRETTY_PRINT));
    header('Location: inquiries.php?msg=deleted');
    exit;
}

// Handle Status Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $inqId = $_POST['inquiry_id'] ?? '';
    $newStatus = $_POST['status'] ?? 'New';
    foreach ($inquiries as &$i) {
        if ($i['id'] === $inqId) {
            $i['status'] = htmlspecialchars($newStatus);
        }
    }
    file_put_contents($dataFile, json_encode($inquiries, JSON_PRETTY_PRINT));
    $message = 'Inquiry status updated successfully!';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Inquiries | Lasanthi Salon Admin</title>
  <link rel="icon" type="image/png" href="../assets/images/logo_transparent.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/admin.css">
  <script>(function(){var t=localStorage.getItem('admin_theme')||'dark';document.documentElement.setAttribute('data-theme',t);})();</script>
</head>
<body>
  <div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="sidebar-brand">
        <img src="../assets/images/logo_transparent.png" alt="Lasanthi Salon Logo" class="sidebar-logo">
        <div class="sidebar-title">Lasanthi Salon</div>
        <div class="sidebar-sub">Admin Panel</div>
      </div>

      <ul class="sidebar-nav">
        <li class="nav-item">
          <a href="index.php"><i class="fa-solid fa-chart-line"></i> <span>Dashboard</span></a>
        </li>
        <li class="nav-item">
          <a href="banner_videos.php"><i class="fa-solid fa-film"></i> <span>Banner Videos</span></a>
        </li>
        <li class="nav-item">
          <a href="videos.php"><i class="fa-solid fa-video"></i> <span>Showcase Videos</span></a>
        </li>
        <li class="nav-item">
          <a href="bookings.php"><i class="fa-solid fa-calendar-check"></i> <span>Bookings</span></a>
        </li>
        <li class="nav-item active">
          <a href="inquiries.php"><i class="fa-solid fa-envelope"></i> <span>Contact Inquiries</span></a>
        </li>
        <li class="nav-item">
          <a href="gallery.php"><i class="fa-solid fa-images"></i> <span>Gallery Photos</span></a>
        </li>
        <li class="nav-item">
          <a href="testimonials.php"><i class="fa-solid fa-star"></i> <span>Testimonials</span></a>
        </li>
        <li class="nav-item" style="margin-top: 20px; border-top: 1px solid var(--admin-border); padding-top: 14px;">
          <a href="../index.html" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> <span>View Live Website</span></a>
        </li>
        <li class="nav-item"><a href="javascript:void(0)" id="admin-theme-toggle"><i class="fa-solid fa-circle-half-stroke"></i> <span>Light / Dark Theme</span></a></li>
        <li class="nav-item"><a href="logout.php" style="color: #e74c3c;"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
        </li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
      <div class="admin-header">
        <div>
          <h1 class="admin-page-title">Contact Messages & Inquiries</h1>
          <p class="admin-page-sub">Client submissions from the website contact form.</p>
        </div>
      </div>

      <?php if (!empty($message)): ?>
        <div class="alert-box alert-success"><i class="fa-solid fa-check"></i> <?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <div class="admin-card">
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Date / Time</th>
                <th>Sender Name</th>
                <th>Phone Number</th>
                <th>Interested In</th>
                <th>Message</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($inquiries)): ?>
                <tr><td colspan="7" style="text-align: center; color: var(--admin-muted); padding: 30px;">No contact inquiries received yet.</td></tr>
              <?php else: ?>
                <?php foreach ($inquiries as $i): ?>
                  <tr>
                    <td style="font-size: 0.8rem; color: var(--admin-muted); white-space: nowrap;">
                      <?= htmlspecialchars($i['created_at'] ?? 'Recent') ?>
                    </td>
                    <td><strong style="color: #fff;"><?= htmlspecialchars($i['name']) ?></strong></td>
                    <td>
                      <a href="tel:<?= htmlspecialchars($i['phone']) ?>" style="color: var(--admin-gold); font-weight: 600;">
                        <?= htmlspecialchars($i['phone']) ?>
                      </a>
                    </td>
                    <td><span style="color: var(--admin-gold);"><?= htmlspecialchars($i['interest']) ?></span></td>
                    <td style="max-width: 280px; font-size: 0.86rem; color: #c4baac;">
                      <?= htmlspecialchars($i['message']) ?>
                    </td>
                    <td>
                      <form method="POST" action="inquiries.php" style="margin: 0;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="inquiry_id" value="<?= htmlspecialchars($i['id']) ?>">
                        <select name="status" class="admin-select" onchange="this.form.submit()" style="padding: 4px 8px; font-size: 0.78rem; width: auto;">
                          <option value="New" <?= ($i['status'] ?? '') === 'New' ? 'selected' : '' ?>>New</option>
                          <option value="Replied" <?= ($i['status'] ?? '') === 'Replied' ? 'selected' : '' ?>>Replied</option>
                          <option value="Archived" <?= ($i['status'] ?? '') === 'Archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                      </form>
                    </td>
                    <td>
                      <div style="display: flex; gap: 8px;">
                        <?php
                          $cleanPhone = preg_replace('/[^0-9]/', '', $i['phone']);
                          if (str_starts_with($cleanPhone, '0')) {
                              $cleanPhone = '94' . substr($cleanPhone, 1);
                          }
                          $waMsg = urlencode("Hello " . $i['name'] . ", this is Lasanthi Bandaranayaka Salon & Academy Dambulla replying to your message regarding " . $i['interest'] . ".");
                        ?>
                        <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= $waMsg ?>" target="_blank" class="btn-admin btn-admin-wa">
                          <i class="fa-brands fa-whatsapp"></i> Chat
                        </a>
                        <a href="inquiries.php?delete=<?= urlencode($i['id']) ?>" onclick="return confirm('Delete this inquiry?')" class="btn-admin btn-admin-delete">
                          <i class="fa-solid fa-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
  <script src="assets/admin.js"></script>
</body>
</html>

