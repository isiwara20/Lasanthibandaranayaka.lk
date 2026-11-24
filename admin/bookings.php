<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

$dataFile = __DIR__ . '/../data/bookings.json';
$bookings = json_decode(@file_get_contents($dataFile), true) ?: [];

$message = '';

// Handle Status Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $bookId = $_POST['booking_id'] ?? '';
    $newStatus = $_POST['status'] ?? 'New';
    foreach ($bookings as &$b) {
        if ($b['id'] === $bookId) {
            $b['status'] = htmlspecialchars($newStatus);
        }
    }
    file_put_contents($dataFile, json_encode($bookings, JSON_PRETTY_PRINT));
    $message = 'Booking status updated successfully!';
}

// Handle Delete
if (isset($_GET['delete'])) {
    $delId = $_GET['delete'];
    $bookings = array_values(array_filter($bookings, fn($b) => $b['id'] !== $delId));
    file_put_contents($dataFile, json_encode($bookings, JSON_PRETTY_PRINT));
    header('Location: bookings.php?msg=deleted');
    exit;
}

// Filter
$filter = $_GET['status'] ?? 'all';
$filteredBookings = $bookings;
if ($filter !== 'all') {
    $filteredBookings = array_filter($bookings, fn($b) => strtolower($b['status'] ?? '') === strtolower($filter));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bookings & Appointments | Lasanthi Salon Admin</title>
  <link rel="icon" type="image/png" href="../assets/images/logo_transparent.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/admin.css">
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
        <li class="nav-item active">
          <a href="bookings.php"><i class="fa-solid fa-calendar-check"></i> <span>Bookings</span></a>
        </li>
        <li class="nav-item">
          <a href="inquiries.php"><i class="fa-solid fa-envelope"></i> <span>Contact Inquiries</span></a>
        </li>
        <li class="nav-item">
          <a href="gallery.php"><i class="fa-solid fa-images"></i> <span>Gallery Photos</span></a>
        </li>
        <li class="nav-item" style="margin-top: 20px; border-top: 1px solid var(--admin-border); padding-top: 14px;">
          <a href="../index.html" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> <span>View Live Website</span></a>
        </li>
        <li class="nav-item">
          <a href="logout.php" style="color: #e74c3c;"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a>
        </li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
      <div class="admin-header">
        <div>
          <h1 class="admin-page-title">Appointment Bookings</h1>
          <p class="admin-page-sub">View and manage salon appointments, change status, and contact clients directly via WhatsApp.</p>
        </div>
      </div>

      <?php if (!empty($message)): ?>
        <div class="alert-box alert-success"><i class="fa-solid fa-check"></i> <?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <!-- Filter Bar -->
      <div style="display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap;">
        <a href="bookings.php?status=all" class="btn-admin <?= $filter === 'all' ? 'btn-admin-gold' : '' ?>" style="<?= $filter !== 'all' ? 'background: #18130f; border: 1px solid var(--admin-border); color: #c4baac;' : '' ?>">All (<?= count($bookings) ?>)</a>
        <a href="bookings.php?status=new" class="btn-admin <?= $filter === 'new' ? 'btn-admin-gold' : '' ?>" style="<?= $filter !== 'new' ? 'background: #18130f; border: 1px solid var(--admin-border); color: #c4baac;' : '' ?>">New</a>
        <a href="bookings.php?status=confirmed" class="btn-admin <?= $filter === 'confirmed' ? 'btn-admin-gold' : '' ?>" style="<?= $filter !== 'confirmed' ? 'background: #18130f; border: 1px solid var(--admin-border); color: #c4baac;' : '' ?>">Confirmed</a>
        <a href="bookings.php?status=completed" class="btn-admin <?= $filter === 'completed' ? 'btn-admin-gold' : '' ?>" style="<?= $filter !== 'completed' ? 'background: #18130f; border: 1px solid var(--admin-border); color: #c4baac;' : '' ?>">Completed</a>
      </div>

      <!-- Bookings Table Card -->
      <div class="admin-card">
        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Booking Date / Time</th>
                <th>Client Name</th>
                <th>Phone Number</th>
                <th>Requested Service</th>
                <th>Preferred Date</th>
                <th>Notes / Requirements</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($filteredBookings)): ?>
                <tr><td colspan="8" style="text-align: center; color: var(--admin-muted); padding: 30px;">No bookings found for this filter.</td></tr>
              <?php else: ?>
                <?php foreach ($filteredBookings as $b): ?>
                  <tr>
                    <td style="font-size: 0.8rem; color: var(--admin-muted); white-space: nowrap;">
                      <?= htmlspecialchars($b['created_at'] ?? 'Recent') ?>
                    </td>
                    <td><strong style="color: #fff; font-size: 0.95rem;"><?= htmlspecialchars($b['name']) ?></strong></td>
                    <td>
                      <a href="tel:<?= htmlspecialchars($b['phone']) ?>" style="color: var(--admin-gold); font-weight: 600;">
                        <?= htmlspecialchars($b['phone']) ?>
                      </a>
                    </td>
                    <td>
                      <span style="background: rgba(212,175,55,0.1); border: 1px solid var(--admin-border); color: var(--admin-gold); padding: 4px 10px; border-radius: 6px; font-size: 0.82rem; font-weight: 600;">
                        <?= htmlspecialchars($b['service']) ?>
                      </span>
                    </td>
                    <td><strong style="color: #fff;"><?= htmlspecialchars($b['date'] ?: 'Flexible') ?></strong></td>
                    <td style="max-width: 220px; font-size: 0.84rem; color: #c4baac;">
                      <?= htmlspecialchars($b['notes'] ?: 'None') ?>
                    </td>
                    <td>
                      <form method="POST" action="bookings.php" style="margin: 0;">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($b['id']) ?>">
                        <select name="status" class="admin-select" onchange="this.form.submit()" style="padding: 4px 8px; font-size: 0.78rem; width: auto;">
                          <option value="New" <?= ($b['status'] ?? '') === 'New' ? 'selected' : '' ?>>New</option>
                          <option value="Confirmed" <?= ($b['status'] ?? '') === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                          <option value="Completed" <?= ($b['status'] ?? '') === 'Completed' ? 'selected' : '' ?>>Completed</option>
                          <option value="Cancelled" <?= ($b['status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                      </form>
                    </td>
                    <td>
                      <div style="display: flex; gap: 8px; align-items: center;">
                        <?php
                          $cleanPhone = preg_replace('/[^0-9]/', '', $b['phone']);
                          if (str_starts_with($cleanPhone, '0')) {
                              $cleanPhone = '94' . substr($cleanPhone, 1);
                          }
                          $waMsg = urlencode("Hello " . $b['name'] . ", this is Lasanthi Bandaranayaka Salon & Academy Dambulla. We received your booking for " . $b['service'] . " on " . ($b['date'] ?: 'your preferred date') . ". We are pleased to assist you!");
                        ?>
                        <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= $waMsg ?>" target="_blank" class="btn-admin btn-admin-wa" title="Chat on WhatsApp">
                          <i class="fa-brands fa-whatsapp"></i> Chat
                        </a>
                        <a href="bookings.php?delete=<?= urlencode($b['id']) ?>" onclick="return confirm('Delete this booking record?')" class="btn-admin btn-admin-delete" title="Delete">
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
</body>
</html>
