<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

$dataFile = __DIR__ . '/../data/videos.json';
$videos = json_decode(@file_get_contents($dataFile), true) ?: [];

$message = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $delId = $_GET['delete'];
    $videos = array_values(array_filter($videos, fn($v) => ($v['id'] ?? '') !== $delId));
    file_put_contents($dataFile, json_encode($videos, JSON_PRETTY_PRINT));
    header('Location: videos.php?msg=deleted');
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Video successfully removed from showcase!';
}

// Handle Add New Video
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_video') {
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $badge = trim($_POST['badge'] ?? 'Bridal Reel');
    $videoUrl = trim($_POST['video_url'] ?? '');
    $posterUrl = trim($_POST['poster_url'] ?? 'assets/images/bridal_1.jpg');

    // Handle File Upload if provided
    if (!empty($_FILES['video_file']['name'])) {
        $file = $_FILES['video_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['mp4', 'webm', 'mov', 'ogg'];

        if (!in_array($ext, $allowed)) {
            $error = 'Invalid video format. Allowed formats: MP4, WEBM, MOV.';
        } else {
            $filename = 'reel_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $dest = __DIR__ . '/../uploads/videos/' . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $videoUrl = 'uploads/videos/' . $filename;
            } else {
                $error = 'Failed to upload video file. Check uploads directory permissions.';
            }
        }
    }

    // Handle Poster Upload if provided
    if (empty($error) && !empty($_FILES['poster_file']['name'])) {
        $pFile = $_FILES['poster_file'];
        $pExt = strtolower(pathinfo($pFile['name'], PATHINFO_EXTENSION));
        $pAllowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($pExt, $pAllowed)) {
            $pFilename = 'poster_' . time() . '_' . rand(100, 999) . '.' . $pExt;
            $pDest = __DIR__ . '/../uploads/gallery/' . $pFilename;
            if (move_uploaded_file($pFile['tmp_name'], $pDest)) {
                $posterUrl = 'uploads/gallery/' . $pFilename;
            }
        }
    }

    if (empty($error)) {
        if (empty($title)) {
            $error = 'Please enter a title for the video.';
        } elseif (empty($videoUrl)) {
            $error = 'Please either upload a video file or provide a video path / URL.';
        } else {
            $newVid = [
                'id' => 'vid_' . time(),
                'title' => htmlspecialchars($title),
                'subtitle' => htmlspecialchars($subtitle),
                'badge' => htmlspecialchars($badge ?: 'Showcase Reel'),
                'video_url' => htmlspecialchars($videoUrl),
                'poster_url' => htmlspecialchars($posterUrl),
                'created_at' => date('Y-m-d H:i:s')
            ];

            array_push($videos, $newVid);
            file_put_contents($dataFile, json_encode($videos, JSON_PRETTY_PRINT));
            $message = 'Showcase video successfully added to the carousel!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Showcase Videos Manager | Lasanthi Salon Admin</title>
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
        <li class="nav-item active">
          <a href="videos.php"><i class="fa-solid fa-video"></i> <span>Showcase Videos</span></a>
        </li>
        <li class="nav-item">
          <a href="gallery.php"><i class="fa-solid fa-images"></i> <span>Gallery Photos</span></a>
        </li>
        <li class="nav-item">
          <a href="testimonials.php"><i class="fa-solid fa-star"></i> <span>Testimonials</span></a>
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
          <h1 class="admin-page-title">Showcase Videos Manager</h1>
          <p class="admin-page-sub">Manage the "Videos in Motion" slider shown on the live home page (displays 3 videos by default with slider arrows).</p>
        </div>
        <div>
          <a href="#upload-card" class="btn-admin btn-admin-gold">
            <i class="fa-solid fa-plus"></i>
            <span>Add New Video</span>
          </a>
        </div>
      </div>

      <?php if (!empty($message)): ?>
        <div class="alert-box alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="alert-box alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- Upload Form Card -->
      <div class="admin-card" id="upload-card">
        <div class="card-head">
          <h2 class="card-title"><i class="fa-solid fa-cloud-arrow-up text-gold"></i> Add New Showcase Video</h2>
        </div>
        <div class="card-body">
          <form method="POST" action="videos.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add_video">

            <div class="form-grid">
              <div class="admin-form-group">
                <label class="admin-label" for="title">Video Title *</label>
                <input type="text" id="title" name="title" class="admin-input" placeholder="e.g. Royal Kandyan Saree Draping" required>
              </div>

              <div class="admin-form-group">
                <label class="admin-label" for="badge">Badge Tag</label>
                <input type="text" id="badge" name="badge" class="admin-input" placeholder="e.g. Bridal Reel 4, Hair Glow">
              </div>
            </div>

            <div class="admin-form-group">
              <label class="admin-label" for="subtitle">Short Description / Subtitle</label>
              <input type="text" id="subtitle" name="subtitle" class="admin-input" placeholder="e.g. Intricate jewelry placement, nalalpatiya and hair dressing.">
            </div>

            <div class="form-grid">
              <div class="admin-form-group">
                <label class="admin-label">Option A: Upload MP4 / WEBM File</label>
                <input type="file" name="video_file" accept="video/mp4,video/webm,video/quicktime" class="admin-input" style="padding: 10px;">
              </div>

              <div class="admin-form-group">
                <label class="admin-label" for="video_url">Option B: Video Path / URL (if already uploaded)</label>
                <input type="text" id="video_url" name="video_url" class="admin-input" placeholder="assets/videos/bridal_reel_1.mp4">
              </div>
            </div>

            <div class="admin-form-group">
              <label class="admin-label">Cover / Poster Image (Optional)</label>
              <input type="file" name="poster_file" accept="image/*" class="admin-input" style="padding: 10px;">
            </div>

            <button type="submit" class="btn-admin btn-admin-gold">
              <i class="fa-solid fa-upload"></i>
              <span>Save & Publish to Carousel</span>
            </button>
          </form>
        </div>
      </div>

      <!-- Live Videos Showcase Grid -->
      <div class="admin-card">
        <div class="card-head">
          <h2 class="card-title"><i class="fa-solid fa-film text-gold"></i> Live Showcase Carousel Videos (<?= count($videos) ?> total)</h2>
          <span style="font-size: 0.85rem; color: var(--admin-muted);">By default, 3 videos are shown at once with next/prev slider controls</span>
        </div>
        <div class="card-body">
          <?php if (empty($videos)): ?>
            <p style="text-align: center; color: var(--admin-muted); padding: 40px 0;">No showcase videos configured yet. Add your first video above.</p>
          <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
              <?php foreach ($videos as $idx => $v): ?>
                <div style="background: var(--admin-surface); border: 1px solid var(--admin-border); border-radius: 14px; overflow: hidden; display: flex; flex-direction: column;">
                  <div style="position: relative; height: 220px; background: #000;">
                    <video src="../<?= htmlspecialchars($v['video_url']) ?>" poster="../<?= htmlspecialchars($v['poster_url'] ?? '') ?>" controls preload="metadata" style="width: 100%; height: 100%; object-fit: cover;"></video>
                    <span style="position: absolute; top: 12px; left: 12px; background: rgba(0,0,0,0.75); color: #d4af37; padding: 4px 12px; border-radius: 20px; font-size: 0.76rem; border: 1px solid rgba(212,175,55,0.4); pointer-events: none;">
                      <?= htmlspecialchars($v['badge'] ?? 'Reel ' . ($idx + 1)) ?>
                    </span>
                  </div>
                  <div style="padding: 18px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                      <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--admin-text); margin-bottom: 6px;">
                        <?= htmlspecialchars($v['title'] ?? 'Video ' . ($idx + 1)) ?>
                      </h3>
                      <p style="font-size: 0.85rem; color: var(--admin-muted); line-height: 1.5; margin-bottom: 12px;">
                        <?= htmlspecialchars($v['subtitle'] ?? '') ?>
                      </p>
                      <div style="font-size: 0.76rem; color: var(--admin-muted); word-break: break-all;">
                        <i class="fa-solid fa-link text-gold"></i> <?= htmlspecialchars($v['video_url']) ?>
                      </div>
                    </div>
                    <div style="margin-top: 16px; padding-top: 12px; border-top: 1px solid var(--admin-border); display: flex; justify-content: space-between; align-items: center;">
                      <span style="font-size: 0.78rem; color: var(--admin-muted);">Slide #<?= $idx + 1 ?></span>
                      <a href="videos.php?delete=<?= urlencode($v['id']) ?>" onclick="return confirm('Are you sure you want to remove this video from the carousel?');" class="btn-admin-delete">
                        <i class="fa-solid fa-trash"></i> Delete
                      </a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>

  <script src="assets/admin.js"></script>
</body>
</html>
