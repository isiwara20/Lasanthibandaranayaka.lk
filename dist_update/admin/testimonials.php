<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

$dataFile = __DIR__ . '/../data/testimonials.json';
$items = [];
if (file_exists($dataFile)) {
    $items = json_decode(file_get_contents($dataFile), true) ?: [];
}

$message = '';
$error = '';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? '';

    if ($action === 'approve' && !empty($id)) {
        foreach ($items as &$item) {
            if ($item['id'] === $id) {
                $item['status'] = 'approved';
                break;
            }
        }
        file_put_contents($dataFile, json_encode($items, JSON_PRETTY_PRINT));
        $message = 'Testimonial approved and published live to the website!';
    } elseif ($action === 'reject' && !empty($id)) {
        foreach ($items as &$item) {
            if ($item['id'] === $id) {
                $item['status'] = 'rejected';
                break;
            }
        }
        file_put_contents($dataFile, json_encode($items, JSON_PRETTY_PRINT));
        $message = 'Testimonial has been rejected and will not be displayed on the website.';
    } elseif ($action === 'delete' && !empty($id)) {
        $items = array_values(array_filter($items, function($item) use ($id) {
            return $item['id'] !== $id;
        }));
        file_put_contents($dataFile, json_encode($items, JSON_PRETTY_PRINT));
        $message = 'Testimonial deleted successfully.';
    } elseif ($action === 'add_manual') {
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? 'Valued Bride');
        $location = trim($_POST['location'] ?? 'Dambulla');
        $service = trim($_POST['service'] ?? 'Bridal Dressing');
        $rating = intval($_POST['rating'] ?? 5);
        $quote = trim($_POST['quote'] ?? '');
        $status = $_POST['status'] ?? 'approved';
        $avatar = 'assets/images/client_nimali.jpg';

        if (!empty($_FILES['photo']['name'])) {
            $file = $_FILES['photo'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($ext, $allowed)) {
                $destDir = __DIR__ . '/../uploads/testimonials/';
                if (!is_dir($destDir)) mkdir($destDir, 0777, true);
                $fn = 'client_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], $destDir . $fn)) {
                    $avatar = 'uploads/testimonials/' . $fn;
                }
            }
        }

        if (empty($name) || empty($quote)) {
            $error = 'Please enter both the customer name and review quote.';
        } else {
            $newReview = [
                'id' => 'test_' . time(),
                'name' => htmlspecialchars($name),
                'role' => htmlspecialchars($role),
                'location' => htmlspecialchars($location),
                'service' => htmlspecialchars($service),
                'rating' => $rating,
                'quote' => htmlspecialchars($quote),
                'avatar' => $avatar,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s')
            ];
            array_unshift($items, $newReview);
            file_put_contents($dataFile, json_encode($items, JSON_PRETTY_PRINT));
            $message = 'Customer testimonial added successfully!';
        }
    }
}

// Compute statistics
$totalCount = count($items);
$pendingCount = 0;
$approvedCount = 0;
$rejectedCount = 0;
$sumRating = 0;

foreach ($items as $it) {
    $st = $it['status'] ?? 'pending';
    if ($st === 'pending') $pendingCount++;
    elseif ($st === 'approved') {
        $approvedCount++;
        $sumRating += ($it['rating'] ?? 5);
    }
    elseif ($st === 'rejected') $rejectedCount++;
}

$avgRating = $approvedCount > 0 ? number_format($sumRating / $approvedCount, 1) : '5.0';

// Current filter
$filter = $_GET['filter'] ?? 'all';
$filteredItems = $items;
if ($filter === 'pending') {
    $filteredItems = array_filter($items, fn($i) => ($i['status'] ?? '') === 'pending');
} elseif ($filter === 'approved') {
    $filteredItems = array_filter($items, fn($i) => ($i['status'] ?? '') === 'approved');
} elseif ($filter === 'rejected') {
    $filteredItems = array_filter($items, fn($i) => ($i['status'] ?? '') === 'rejected');
}

// Get site URL for feedback link
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = rtrim(dirname(dirname($_SERVER['PHP_SELF'] ?? '')), '/\\');
$feedbackUrl = $protocol . $host . ($basePath ? $basePath : '') . '/feedback.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Testimonials & Customer Feedback Manager | Lasanthi Salon Admin</title>
  <link rel="icon" type="image/png" href="../assets/images/logo_gold.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/admin.css">
  <style>
    .share-link-banner {
      background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(26, 21, 16, 0.95) 100%);
      border: 1px solid rgba(212, 175, 55, 0.4);
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px;
      flex-wrap: wrap;
    }
    .share-link-input-group {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-grow: 1;
      max-width: 580px;
    }
    .share-link-input {
      background: rgba(0, 0, 0, 0.5);
      border: 1px solid rgba(212, 175, 55, 0.4);
      border-radius: 8px;
      padding: 10px 14px;
      color: #fff;
      font-size: 0.9rem;
      flex-grow: 1;
      outline: none;
    }
    .testimonial-grid-admin {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
      gap: 20px;
      margin-top: 24px;
    }
    .t-card-admin {
      background: var(--admin-card);
      border: 1px solid var(--admin-border);
      border-radius: 16px;
      padding: 22px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      transition: all 0.3s;
    }
    .t-card-admin:hover {
      border-color: rgba(212, 175, 55, 0.4);
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
    }
    .t-card-admin.pending-card {
      border-color: #f1c40f;
      background: linear-gradient(180deg, rgba(241, 196, 15, 0.05) 0%, var(--admin-card) 100%);
    }
    .t-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 14px;
    }
    .t-avatar-box {
      display: flex;
      gap: 12px;
      align-items: center;
    }
    .t-avatar-img {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--admin-gold);
    }
    .t-client-name {
      font-weight: 700;
      font-size: 1.05rem;
      color: var(--admin-text);
    }
    .t-client-meta {
      font-size: 0.8rem;
      color: var(--admin-muted);
    }
    .t-stars {
      color: #f39c12;
      font-size: 0.95rem;
      margin-bottom: 10px;
    }
    .t-quote {
      font-size: 0.92rem;
      line-height: 1.6;
      color: var(--admin-text);
      font-style: italic;
      margin-bottom: 18px;
      background: rgba(255, 255, 255, 0.03);
      padding: 12px;
      border-radius: 8px;
      border-left: 3px solid var(--admin-gold);
    }
    .t-footer-actions {
      display: flex;
      gap: 8px;
      align-items: center;
      border-top: 1px solid var(--admin-border);
      padding-top: 14px;
      flex-wrap: wrap;
    }
    .badge-status {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 0.76rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .badge-pending {
      background: rgba(241, 196, 15, 0.15);
      color: #f1c40f;
      border: 1px solid #f1c40f;
    }
    .badge-approved {
      background: rgba(46, 204, 113, 0.15);
      color: #2ecc71;
      border: 1px solid #2ecc71;
    }
    .badge-rejected {
      background: rgba(231, 76, 60, 0.15);
      color: #e74c3c;
      border: 1px solid #e74c3c;
    }
    .filter-tabs {
      display: flex;
      gap: 10px;
      border-bottom: 1px solid var(--admin-border);
      padding-bottom: 12px;
      margin-bottom: 20px;
      overflow-x: auto;
    }
    .filter-tab-btn {
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 0.88rem;
      font-weight: 600;
      color: var(--admin-muted);
      text-decoration: none;
      transition: all 0.25s;
    }
    .filter-tab-btn.active, .filter-tab-btn:hover {
      background: var(--admin-gold);
      color: #120e06;
    }
  </style>
</head>
<body>
  <div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="brand">
        <img src="../assets/images/logo_transparent.png" alt="Lasanthi Salon" style="height: 50px;">
        <span class="brand-text">Staff Admin</span>
      </div>

      <ul class="nav-menu">
        <li class="nav-item">
          <a href="index.php"><i class="fa-solid fa-chart-line"></i> <span>Dashboard Overview</span></a>
        </li>
        <li class="nav-item">
          <a href="banner_videos.php"><i class="fa-solid fa-clapperboard"></i> <span>Hero Banner Videos</span></a>
        </li>
        <li class="nav-item">
          <a href="videos.php"><i class="fa-solid fa-video"></i> <span>Showcase Videos</span></a>
        </li>
        <li class="nav-item">
          <a href="gallery.php"><i class="fa-solid fa-images"></i> <span>Gallery Photos</span></a>
        </li>
        <li class="nav-item active">
          <a href="testimonials.php">
            <i class="fa-solid fa-star"></i> <span>Testimonials</span>
            <?php if ($pendingCount > 0): ?>
              <span style="background: #f1c40f; color: #120e08; font-size: 0.72rem; padding: 2px 7px; border-radius: 10px; font-weight: 800; margin-left: auto;"><?= $pendingCount ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item">
          <a href="bookings.php"><i class="fa-solid fa-calendar-check"></i> <span>Bookings</span></a>
        </li>
        <li class="nav-item">
          <a href="inquiries.php"><i class="fa-solid fa-envelope"></i> <span>Contact Inquiries</span></a>
        </li>
        <li class="nav-item" style="margin-top: 20px; border-top: 1px solid var(--admin-border); padding-top: 14px;">
          <a href="../index.html" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> <span>View Live Website</span></a>
        </li>
        <li class="nav-item"><a href="javascript:void(0)" id="admin-theme-toggle"><i class="fa-solid fa-circle-half-stroke"></i> <span>Light / Dark Theme</span></a></li>
        <li class="nav-item"><a href="logout.php" style="color: #e74c3c;"><i class="fa-solid fa-right-from-bracket"></i> <span>Logout</span></a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
      <div class="admin-header">
        <div>
          <h1 class="admin-page-title">Client Reviews & Testimonials</h1>
          <p class="admin-page-sub">Approve incoming customer feedback, manage published reviews, and share review links with brides.</p>
        </div>
        <div>
          <a href="#manual-card" class="btn-admin btn-admin-gold">
            <i class="fa-solid fa-plus"></i>
            <span>Add Review Manually</span>
          </a>
        </div>
      </div>

      <?php if (!empty($message)): ?>
        <div class="alert-box alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="alert-box alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- Customer Link Share Banner -->
      <div class="share-link-banner">
        <div>
          <div style="font-size: 1.15rem; font-weight: 700; color: #fff; margin-bottom: 4px;">
            <i class="fa-solid fa-paper-plane text-gold"></i> Send Review Link to Brides & Clients
          </div>
          <div style="color: #c9bdae; font-size: 0.88rem;">
            Copy this direct link or send it to your clients over WhatsApp right after their salon appointment or wedding day.
          </div>
        </div>

        <div class="share-link-input-group">
          <input type="text" id="feedback-url-input" class="share-link-input" value="<?= htmlspecialchars($feedbackUrl) ?>" readonly>
          <button class="btn-admin btn-admin-gold" onclick="copyFeedbackUrl()">
            <i class="fa-solid fa-copy"></i> <span id="copy-btn-text">Copy Link</span>
          </button>
          <a href="https://wa.me/?text=<?= urlencode("Dear valued client, thank you for choosing Lasanthi Bandaranayaka Salon & Academy! We would love if you could share a few words about your royal experience with us: " . $feedbackUrl) ?>" target="_blank" rel="noopener" class="btn-admin" style="background: #25d366; color: #fff;">
            <i class="fa-brands fa-whatsapp"></i> <span>Share via WhatsApp</span>
          </a>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="stats-row" style="margin-bottom: 26px;">
        <div class="stat-box">
          <div class="stat-title">Pending Staff Approval</div>
          <div class="stat-value" style="color: #f1c40f;"><?= $pendingCount ?></div>
          <div style="font-size: 0.8rem; color: var(--admin-muted);">Awaiting your review</div>
        </div>
        <div class="stat-box">
          <div class="stat-title">Approved & Live</div>
          <div class="stat-value" style="color: #2ecc71;"><?= $approvedCount ?></div>
          <div style="font-size: 0.8rem; color: var(--admin-muted);">Currently shown on website</div>
        </div>
        <div class="stat-box">
          <div class="stat-title">Average Client Rating</div>
          <div class="stat-value" style="color: var(--admin-gold);">
            <?= $avgRating ?> <i class="fa-solid fa-star" style="font-size: 1.2rem;"></i>
          </div>
          <div style="font-size: 0.8rem; color: var(--admin-muted);">Across approved reviews</div>
        </div>
        <div class="stat-box">
          <div class="stat-title">Total Submissions</div>
          <div class="stat-value"><?= $totalCount ?></div>
          <div style="font-size: 0.8rem; color: var(--admin-muted);">All time records</div>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="filter-tabs">
        <a href="testimonials.php?filter=all" class="filter-tab-btn <?= $filter === 'all' ? 'active' : '' ?>">All Reviews (<?= $totalCount ?>)</a>
        <a href="testimonials.php?filter=pending" class="filter-tab-btn <?= $filter === 'pending' ? 'active' : '' ?>">
          Pending Approval (<?= $pendingCount ?>)
        </a>
        <a href="testimonials.php?filter=approved" class="filter-tab-btn <?= $filter === 'approved' ? 'active' : '' ?>">Approved & Live (<?= $approvedCount ?>)</a>
        <a href="testimonials.php?filter=rejected" class="filter-tab-btn <?= $filter === 'rejected' ? 'active' : '' ?>">Rejected (<?= $rejectedCount ?>)</a>
      </div>

      <!-- Testimonials Grid -->
      <div class="testimonial-grid-admin">
        <?php if (empty($filteredItems)): ?>
          <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--admin-muted);">
            <i class="fa-regular fa-comment-dots" style="font-size: 3rem; margin-bottom: 12px;"></i>
            <p>No testimonials found in this category.</p>
          </div>
        <?php else: ?>
          <?php foreach ($filteredItems as $t): ?>
            <?php 
              $st = $t['status'] ?? 'pending';
              $badgeClass = $st === 'approved' ? 'badge-approved' : ($st === 'rejected' ? 'badge-rejected' : 'badge-pending');
              $badgeIcon = $st === 'approved' ? 'fa-circle-check' : ($st === 'rejected' ? 'fa-circle-xmark' : 'fa-clock');
            ?>
            <div class="t-card-admin <?= $st === 'pending' ? 'pending-card' : '' ?>">
              <div>
                <div class="t-header">
                  <div class="t-avatar-box">
                    <img src="../<?= htmlspecialchars($t['avatar'] ?? 'assets/images/client_nimali.jpg') ?>" alt="Client" class="t-avatar-img">
                    <div>
                      <div class="t-client-name"><?= htmlspecialchars($t['name']) ?></div>
                      <div class="t-client-meta"><?= htmlspecialchars($t['role']) ?> • <?= htmlspecialchars($t['location'] ?? 'Dambulla') ?></div>
                      <div class="t-client-meta" style="color: var(--admin-gold);"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($t['service'] ?? 'Salon Service') ?></div>
                    </div>
                  </div>
                  <span class="badge-status <?= $badgeClass ?>">
                    <i class="fa-solid <?= $badgeIcon ?>"></i> <?= ucfirst($st) ?>
                  </span>
                </div>

                <div class="t-stars">
                  <?php for ($i = 0; $i < ($t['rating'] ?? 5); $i++): ?>
                    <i class="fa-solid fa-star"></i>
                  <?php endfor; ?>
                </div>

                <div class="t-quote">
                  &ldquo;<?= htmlspecialchars($t['quote']) ?>&rdquo;
                </div>
              </div>

              <div>
                <div style="font-size: 0.75rem; color: var(--admin-muted); margin-bottom: 10px;">
                  <i class="fa-regular fa-calendar"></i> Submitted: <?= htmlspecialchars($t['created_at'] ?? 'Recently') ?>
                </div>

                <div class="t-footer-actions">
                  <?php if ($st === 'pending' || $st === 'rejected'): ?>
                    <form method="POST" action="testimonials.php?filter=<?= $filter ?>" style="display: inline;">
                      <input type="hidden" name="action" value="approve">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($t['id']) ?>">
                      <button type="submit" class="btn-admin" style="background: #27ae60; color: #fff; padding: 6px 12px; font-size: 0.8rem;">
                        <i class="fa-solid fa-check"></i> Approve & Publish
                      </button>
                    </form>
                  <?php endif; ?>

                  <?php if ($st === 'pending' || $st === 'approved'): ?>
                    <form method="POST" action="testimonials.php?filter=<?= $filter ?>" style="display: inline;">
                      <input type="hidden" name="action" value="reject">
                      <input type="hidden" name="id" value="<?= htmlspecialchars($t['id']) ?>">
                      <button type="submit" class="btn-admin" style="background: rgba(231, 76, 60, 0.2); color: #e74c3c; border: 1px solid #e74c3c; padding: 6px 12px; font-size: 0.8rem;">
                        <i class="fa-solid fa-ban"></i> Reject
                      </button>
                    </form>
                  <?php endif; ?>

                  <form method="POST" action="testimonials.php?filter=<?= $filter ?>" onsubmit="return confirm('Are you sure you want to permanently delete this testimonial?');" style="display: inline; margin-left: auto;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($t['id']) ?>">
                    <button type="submit" class="btn-admin" style="background: transparent; color: #888; padding: 6px 10px; font-size: 0.8rem;">
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Add Testimonial Manually Card -->
      <div class="admin-card" id="manual-card" style="margin-top: 40px;">
        <div class="card-head">
          <h2 class="card-title"><i class="fa-solid fa-pen-nib text-gold"></i> Add Review Manually (from Google, Facebook or WhatsApp)</h2>
        </div>
        <div class="card-body">
          <form method="POST" action="testimonials.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_manual">

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Client Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Ruwani Alwis" required>
              </div>

              <div class="form-group">
                <label class="form-label">Role / Occasion</label>
                <input type="text" name="role" class="form-control" placeholder="e.g. Kandyan Bride, Salon Guest" value="Kandyan Bride">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Service Received</label>
                <input type="text" name="service" class="form-control" placeholder="e.g. Bridal Dressing & Regalia" value="Bridal Dressing">
              </div>

              <div class="form-group">
                <label class="form-label">Client Location / City</label>
                <input type="text" name="location" class="form-control" placeholder="e.g. Dambulla, Kandy" value="Dambulla">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Rating</label>
                <select name="rating" class="form-control">
                  <option value="5">★★★★★ (5 Stars - Exceptional)</option>
                  <option value="4">★★★★☆ (4 Stars - Very Good)</option>
                  <option value="3">★★★☆☆ (3 Stars - Good)</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Publication Status</label>
                <select name="status" class="form-control">
                  <option value="approved">Approve Immediately (Live on Website)</option>
                  <option value="pending">Save as Pending (Staff Review)</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Testimonial Quote / Story *</label>
              <textarea name="quote" class="form-control" rows="3" placeholder="Enter customer review text here..." required></textarea>
            </div>

            <div class="form-group">
              <label class="form-label">Client Photo / Avatar (Optional)</label>
              <input type="file" name="photo" class="form-control" accept="image/*">
            </div>

            <button type="submit" class="btn-admin btn-admin-gold" style="margin-top: 10px;">
              <i class="fa-solid fa-plus"></i> Save Testimonial
            </button>
          </form>
        </div>
      </div>
    </main>
  </div>

  <script src="assets/admin.js"></script>
  <script>
    function copyFeedbackUrl() {
      const input = document.getElementById('feedback-url-input');
      input.select();
      input.setSelectionRange(0, 99999);
      navigator.clipboard.writeText(input.value).then(() => {
        const textSpan = document.getElementById('copy-btn-text');
        textSpan.textContent = 'Copied to Clipboard!';
        setTimeout(() => { textSpan.textContent = 'Copy Link'; }, 2500);
      }).catch(err => {
        alert('Copied URL: ' + input.value);
      });
    }
  </script>
</body>
</html>
