<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

$bookings = json_decode(@file_get_contents(__DIR__ . '/../data/bookings.json'), true) ?: [];
$inquiries = json_decode(@file_get_contents(__DIR__ . '/../data/inquiries.json'), true) ?: [];
$videos = json_decode(@file_get_contents(__DIR__ . '/../data/banner_videos.json'), true) ?: [];
$showcaseVideos = json_decode(@file_get_contents(__DIR__ . '/../data/videos.json'), true) ?: [];
$gallery = json_decode(@file_get_contents(__DIR__ . '/../data/gallery.json'), true) ?: [];
$testimonials = json_decode(@file_get_contents(__DIR__ . '/../data/testimonials.json'), true) ?: [];

$newBookingsCount = count(array_filter($bookings, fn($b) => ($b['status'] ?? '') === 'New'));
$newInquiriesCount = count(array_filter($inquiries, fn($i) => ($i['status'] ?? '') === 'New'));
$pendingTestimonialsCount = count(array_filter($testimonials, fn($t) => ($t['status'] ?? '') === 'pending'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | Lasanthi Salon Admin</title>
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
        <li class="nav-item active">
          <a href="index.php"><i class="fa-solid fa-chart-line"></i> <span>Dashboard</span></a>
        </li>
        <li class="nav-item">
          <a href="banner_videos.php"><i class="fa-solid fa-film"></i> <span>Banner Videos</span></a>
        </li>
        <li class="nav-item">
          <a href="videos.php"><i class="fa-solid fa-video"></i> <span>Showcase Videos</span></a>
        </li>
        <li class="nav-item">
          <a href="bookings.php">
            <i class="fa-solid fa-calendar-check"></i> <span>Bookings</span>
            <?php if ($newBookingsCount > 0): ?>
              <span class="nav-badge"><?= $newBookingsCount ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item">
          <a href="inquiries.php">
            <i class="fa-solid fa-envelope"></i> <span>Contact Inquiries</span>
            <?php if ($newInquiriesCount > 0): ?>
              <span class="nav-badge"><?= $newInquiriesCount ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item">
          <a href="gallery.php"><i class="fa-solid fa-images"></i> <span>Gallery Photos</span></a>
        </li>
        <li class="nav-item">
          <a href="testimonials.php">
            <i class="fa-solid fa-star"></i> <span>Testimonials</span>
            <?php if ($pendingTestimonialsCount > 0): ?>
              <span class="nav-badge" style="background: #f1c40f; color: #120e06;"><?= $pendingTestimonialsCount ?></span>
            <?php endif; ?>
          </a>
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
          <h1 class="admin-page-title">Salon Overview</h1>
          <p class="admin-page-sub">Welcome back! Manage banner videos, appointment bookings, inquiries, and gallery images.</p>
        </div>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
          <a href="banner_videos.php" class="btn-admin btn-admin-gold">
            <i class="fa-solid fa-film"></i>
            <span>Banner Videos</span>
          </a>
          <a href="videos.php" class="btn-admin btn-admin-gold">
            <i class="fa-solid fa-video"></i>
            <span>Showcase Videos</span>
          </a>
          <a href="gallery.php" class="btn-admin btn-admin-gold">
            <i class="fa-solid fa-upload"></i>
            <span>Upload Photo</span>
          </a>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="stats-row">
        <div class="stat-box">
          <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
          <div>
            <div class="stat-num"><?= count($bookings) ?></div>
            <div class="stat-title">Total Bookings (<?= $newBookingsCount ?> New)</div>
          </div>
        </div>

        <div class="stat-box">
          <div class="stat-icon"><i class="fa-solid fa-envelope"></i></div>
          <div>
            <div class="stat-num"><?= count($inquiries) ?></div>
            <div class="stat-title">Inquiries (<?= $newInquiriesCount ?> New)</div>
          </div>
        </div>

        <div class="stat-box">
          <div class="stat-icon"><i class="fa-solid fa-film"></i></div>
          <div>
            <div class="stat-num"><?= count($videos) ?></div>
            <div class="stat-title">Hero Banner Videos</div>
          </div>
        </div>

        <div class="stat-box">
          <div class="stat-icon"><i class="fa-solid fa-video"></i></div>
          <div>
            <div class="stat-num"><?= count($showcaseVideos) ?></div>
            <div class="stat-title">Showcase Videos</div>
          </div>
        </div>

        <div class="stat-box">
          <div class="stat-icon"><i class="fa-solid fa-images"></i></div>
          <div>
            <div class="stat-num"><?= count($gallery) ?></div>
            <div class="stat-title">Gallery Photos</div>
          </div>
        </div>

        <div class="stat-box">
          <div class="stat-icon"><i class="fa-solid fa-star"></i></div>
          <div>
            <div class="stat-num"><?= count($testimonials) ?></div>
            <div class="stat-title">Testimonials <?php if ($pendingTestimonialsCount > 0): ?><span style="color: #f1c40f; font-size: 0.8rem;">(<?= $pendingTestimonialsCount ?> pending)</span><?php endif; ?></div>
          </div>
        </div>
      </div>

      <!-- Recent Bookings Table -->
      <div class="admin-card">
        <div class="card-head">
          <h2 class="card-title"><i class="fa-solid fa-calendar-days text-gold"></i> Recent Appointment Bookings</h2>
          <a href="bookings.php" style="color: var(--admin-gold); font-size: 0.86rem; font-weight: 600;">View All (<?= count($bookings) ?>) &rarr;</a>
        </div>

        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Client Name</th>
                <th>Phone Number</th>
                <th>Service</th>
                <th>Preferred Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($bookings)): ?>
                <tr><td colspan="6" style="text-align: center; color: var(--admin-muted); padding: 30px;">No bookings received yet.</td></tr>
              <?php else: ?>
                <?php foreach (array_slice($bookings, 0, 5) as $b): ?>
                  <tr>
                    <td><strong><?= htmlspecialchars($b['name']) ?></strong></td>
                    <td><?= htmlspecialchars($b['phone']) ?></td>
                    <td><span style="color: var(--admin-gold);"><?= htmlspecialchars($b['service']) ?></span></td>
                    <td><?= htmlspecialchars($b['date'] ?: 'Not specified') ?></td>
                    <td>
                      <?php
                        $st = strtolower($b['status'] ?? 'new');
                        $badgeClass = match($st) {
                          'confirmed' => 'badge-confirmed',
                          'completed' => 'badge-completed',
                          'cancelled' => 'badge-cancelled',
                          default => 'badge-new'
                        };
                      ?>
                      <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($b['status'] ?? 'New') ?></span>
                    </td>
                    <td>
                      <?php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $b['phone']);
                        if (str_starts_with($cleanPhone, '0')) {
                            $cleanPhone = '94' . substr($cleanPhone, 1);
                        }
                        $waMsg = urlencode("Hello " . $b['name'] . ", this is Lasanthi Bandaranayaka Salon & Academy Dambulla regarding your appointment booking for " . $b['service'] . ". We are pleased to assist you!");
                      ?>
                      <a href="https://wa.me/<?= $cleanPhone ?>?text=<?= $waMsg ?>" target="_blank" class="btn-admin btn-admin-wa">
                        <i class="fa-brands fa-whatsapp"></i> Chat
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Quick Dual Card Row: Recent Inquiries & Banner Videos Overview -->
      <div style="display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 24px;">
        <!-- Inquiries -->
        <div class="admin-card">
          <div class="card-head">
            <h2 class="card-title"><i class="fa-solid fa-comments text-gold"></i> Latest Inquiries</h2>
            <a href="inquiries.php" style="color: var(--admin-gold); font-size: 0.86rem; font-weight: 600;">View All &rarr;</a>
          </div>
          <?php if (empty($inquiries)): ?>
            <p style="color: var(--admin-muted); padding: 20px 0;">No inquiries received yet.</p>
          <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 14px;">
              <?php foreach (array_slice($inquiries, 0, 3) as $inq): ?>
                <div style="background: rgba(18, 14, 10, 0.6); border: 1px solid var(--admin-border); border-radius: 10px; padding: 14px;">
                  <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                    <strong style="color: #fff;"><?= htmlspecialchars($inq['name']) ?> (<?= htmlspecialchars($inq['phone']) ?>)</strong>
                    <span style="font-size: 0.75rem; color: var(--admin-gold);"><?= htmlspecialchars($inq['interest']) ?></span>
                  </div>
                  <p style="color: #c4baac; font-size: 0.86rem;"><?= htmlspecialchars($inq['message']) ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Banner Videos Overview -->
        <div class="admin-card">
          <div class="card-head">
            <h2 class="card-title"><i class="fa-solid fa-video text-gold"></i> Active Banner Videos</h2>
            <a href="banner_videos.php" style="color: var(--admin-gold); font-size: 0.86rem; font-weight: 600;">Manage Videos &rarr;</a>
          </div>
          <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($videos as $v): ?>
              <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(18, 14, 10, 0.6); border: 1px solid var(--admin-border); border-radius: 10px; padding: 12px;">
                <div>
                  <div style="color: #fff; font-weight: 600; font-size: 0.9rem;"><?= htmlspecialchars($v['title']) ?></div>
                  <div style="font-size: 0.76rem; color: var(--admin-muted);">
                    ðŸ–¥ï¸ Large: <span style="color: var(--admin-gold);"><?= basename($v['video_desktop']) ?></span> | 
                    ðŸ“± Mobile: <span style="color: var(--admin-gold);"><?= basename($v['video_mobile']) ?></span>
                  </div>
                </div>
                <span class="badge badge-confirmed">Active</span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </main>
  </div>
  <script src="assets/admin.js"></script>
</body>
</html>

