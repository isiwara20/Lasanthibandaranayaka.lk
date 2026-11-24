<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

$dataFile = __DIR__ . '/../data/banner_videos.json';
$videos = json_decode(@file_get_contents($dataFile), true) ?: [];

$message = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $delId = $_GET['delete'];
    $videos = array_values(array_filter($videos, fn($v) => $v['id'] !== $delId));
    file_put_contents($dataFile, json_encode($videos, JSON_PRETTY_PRINT));
    header('Location: banner_videos.php?msg=deleted');
    exit;
}

// Handle Status Toggle
if (isset($_GET['toggle'])) {
    $toggleId = $_GET['toggle'];
    foreach ($videos as &$v) {
        if ($v['id'] === $toggleId) {
            $v['active'] = !($v['active'] ?? true);
        }
    }
    file_put_contents($dataFile, json_encode($videos, JSON_PRETTY_PRINT));
    header('Location: banner_videos.php?msg=updated');
    exit;
}

// Handle Form Submission (Add Video)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_video') {
    $title = trim($_POST['title'] ?? '');
    $tag = trim($_POST['tag'] ?? 'Royal Bridal Artistry');
    
    $videoDesktop = '';
    $videoMobile = '';
    $poster = 'assets/images/bridal_1.jpg';

    // 1. Process Desktop Video
    if (!empty($_FILES['video_desktop_file']['name'])) {
        $desktopName = 'desktop_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['video_desktop_file']['name']);
        $targetDesktop = __DIR__ . '/../uploads/videos/' . $desktopName;
        if (move_uploaded_file($_FILES['video_desktop_file']['tmp_name'], $targetDesktop)) {
            $videoDesktop = 'uploads/videos/' . $desktopName;
        }
    } elseif (!empty($_POST['video_desktop_preset'])) {
        $videoDesktop = trim($_POST['video_desktop_preset']);
    }

    // 2. Process Mobile Video
    if (!empty($_FILES['video_mobile_file']['name'])) {
        $mobileName = 'mobile_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['video_mobile_file']['name']);
        $targetMobile = __DIR__ . '/../uploads/videos/' . $mobileName;
        if (move_uploaded_file($_FILES['video_mobile_file']['tmp_name'], $targetMobile)) {
            $videoMobile = 'uploads/videos/' . $mobileName;
        }
    } elseif (!empty($_POST['video_mobile_preset'])) {
        $videoMobile = trim($_POST['video_mobile_preset']);
    }

    // 3. Optional Poster
    if (!empty($_FILES['poster_file']['name'])) {
        $posterName = 'poster_' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['poster_file']['name']);
        $targetPoster = __DIR__ . '/../uploads/gallery/' . $posterName;
        if (move_uploaded_file($_FILES['poster_file']['tmp_name'], $targetPoster)) {
            $poster = 'uploads/gallery/' . $posterName;
        }
    }

    if (empty($title)) {
        $error = 'Please provide a video title.';
    } elseif (empty($videoDesktop) && empty($videoMobile)) {
        $error = 'Please upload or select at least one video (for larger or smaller screens).';
    } else {
        // If one is missing, fall back to the other
        if (empty($videoDesktop)) $videoDesktop = $videoMobile;
        if (empty($videoMobile)) $videoMobile = $videoDesktop;

        $newVid = [
            'id' => 'vid_' . time(),
            'title' => htmlspecialchars($title),
            'tag' => htmlspecialchars($tag),
            'video_desktop' => $videoDesktop,
            'video_mobile' => $videoMobile,
            'poster' => $poster,
            'active' => true,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $videos[] = $newVid;
        file_put_contents($dataFile, json_encode($videos, JSON_PRETTY_PRINT));
        $message = 'New banner video added successfully!';
    }
}

// Available preset videos in project
$presetVideos = [
    'assets/videos/bridal_reel_1.mp4' => 'Bridal Reel 1 - Kandyan Draping',
    'assets/videos/bridal_reel_2.mp4' => 'Bridal Reel 2 - Glam Makeup',
    'assets/videos/bridal_reel_3.mp4' => 'Bridal Reel 3 - Hair & Orchids'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hero Banner Videos | Lasanthi Salon Admin</title>
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
        <li class="nav-item active">
          <a href="banner_videos.php"><i class="fa-solid fa-film"></i> <span>Banner Videos</span></a>
        </li>
        <li class="nav-item">
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
          <h1 class="admin-page-title">Hero Banner Videos</h1>
          <p class="admin-page-sub">Configure auto-playing banner videos with dedicated versions for larger screens and smaller mobile screens.</p>
        </div>
      </div>

      <?php if (!empty($message)): ?>
        <div class="alert-box alert-success"><i class="fa-solid fa-check"></i> <?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="alert-box alert-error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- Add New Video Form -->
      <div class="admin-card">
        <div class="card-head">
          <h2 class="card-title"><i class="fa-solid fa-circle-plus text-gold"></i> Add New Hero Banner Video</h2>
        </div>

        <form method="POST" action="banner_videos.php" enctype="multipart/form-data">
          <input type="hidden" name="action" value="add_video">

          <div class="form-grid" style="margin-bottom: 20px;">
            <div class="admin-form-group">
              <label class="admin-label" for="title">Video Title</label>
              <input type="text" id="title" name="title" class="admin-input" placeholder="e.g. Royal Kandyan Saree Draping & Jewelry" required>
            </div>

            <div class="admin-form-group">
              <label class="admin-label" for="tag">Category / Tag</label>
              <input type="text" id="tag" name="tag" class="admin-input" placeholder="e.g. Royal Bridal Artistry" value="Royal Bridal Artistry">
            </div>
          </div>

          <!-- Dual Video Options: Larger Screens vs Smaller Screens -->
          <div style="background: rgba(18, 14, 10, 0.7); border: 1px solid var(--admin-border); border-radius: 14px; padding: 22px; margin-bottom: 24px;">
            <h3 style="font-family: var(--font-serif); font-size: 1.15rem; color: var(--admin-gold); margin-bottom: 16px;">
              <i class="fa-solid fa-display"></i> Responsive Video Pair Setup
            </h3>

            <div class="form-grid">
              <!-- Larger Screens Video (Desktop) -->
              <div style="border-right: 1px solid var(--admin-border); padding-right: 20px;">
                <label class="admin-label" style="font-size: 0.95rem;">
                  <i class="fa-solid fa-desktop text-gold"></i> 1. Video for Larger Screens (Desktop / Landscape)
                </label>
                <p style="font-size: 0.8rem; color: var(--admin-muted); margin-bottom: 12px;">Used on desktop and laptops (16:9 or horizontal format recommended).</p>

                <div class="admin-form-group">
                  <label style="font-size: 0.8rem; color: #fff;">Upload New MP4 Video File:</label>
                  <input type="file" name="video_desktop_file" accept="video/mp4,video/webm" class="admin-input" style="padding: 8px;">
                </div>

                <div class="admin-form-group" style="margin-bottom: 0;">
                  <label style="font-size: 0.8rem; color: #fff;">Or Select Existing Salon Video:</label>
                  <select name="video_desktop_preset" class="admin-select">
                    <option value="">-- Choose Existing Video --</option>
                    <?php foreach ($presetVideos as $path => $lbl): ?>
                      <option value="<?= $path ?>"><?= $lbl ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <!-- Smaller Screens Video (Mobile) -->
              <div style="padding-left: 10px;">
                <label class="admin-label" style="font-size: 0.95rem;">
                  <i class="fa-solid fa-mobile-screen text-gold"></i> 2. Video for Smaller Screens (Mobile / Portrait)
                </label>
                <p style="font-size: 0.8rem; color: var(--admin-muted); margin-bottom: 12px;">Used on phones and tablets (9:16 vertical reel format recommended).</p>

                <div class="admin-form-group">
                  <label style="font-size: 0.8rem; color: #fff;">Upload New MP4 Video File:</label>
                  <input type="file" name="video_mobile_file" accept="video/mp4,video/webm" class="admin-input" style="padding: 8px;">
                </div>

                <div class="admin-form-group" style="margin-bottom: 0;">
                  <label style="font-size: 0.8rem; color: #fff;">Or Select Existing Salon Video:</label>
                  <select name="video_mobile_preset" class="admin-select">
                    <option value="">-- Choose Existing Video --</option>
                    <?php foreach ($presetVideos as $path => $lbl): ?>
                      <option value="<?= $path ?>"><?= $lbl ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <button type="submit" class="btn-admin btn-admin-gold" style="padding: 12px 28px; font-size: 0.95rem;">
            <i class="fa-solid fa-cloud-arrow-up"></i>
            <span>Save & Publish Banner Video</span>
          </button>
        </form>
      </div>

      <!-- Current Banner Videos List -->
      <div class="admin-card">
        <div class="card-head">
          <h2 class="card-title"><i class="fa-solid fa-list-check text-gold"></i> Current Banner Videos (<?= count($videos) ?>)</h2>
        </div>

        <div class="table-responsive">
          <table class="admin-table">
            <thead>
              <tr>
                <th>Video Preview</th>
                <th>Title & Tag</th>
                <th>Desktop Video (Larger)</th>
                <th>Mobile Video (Smaller)</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($videos)): ?>
                <tr><td colspan="6" style="text-align: center; color: var(--admin-muted); padding: 30px;">No banner videos configured.</td></tr>
              <?php else: ?>
                <?php foreach ($videos as $v): ?>
                  <tr>
                    <td style="width: 140px;">
                      <div style="width: 120px; height: 75px; border-radius: 8px; overflow: hidden; background: #000; border: 1px solid var(--admin-border);">
                        <video src="../<?= htmlspecialchars($v['video_desktop']) ?>" style="width: 100%; height: 100%; object-fit: cover;" muted onmouseover="this.play()" onmouseout="this.pause()"></video>
                      </div>
                    </td>
                    <td>
                      <strong style="color: #fff; font-size: 0.95rem;"><?= htmlspecialchars($v['title']) ?></strong>
                      <div style="font-size: 0.78rem; color: var(--admin-gold);"><?= htmlspecialchars($v['tag'] ?? 'Royal Bridal Artistry') ?></div>
                    </td>
                    <td>
                      <span style="font-family: monospace; font-size: 0.8rem; background: rgba(255,255,255,0.06); padding: 3px 8px; border-radius: 4px;">
                        <i class="fa-solid fa-desktop"></i> <?= basename($v['video_desktop']) ?>
                      </span>
                    </td>
                    <td>
                      <span style="font-family: monospace; font-size: 0.8rem; background: rgba(255,255,255,0.06); padding: 3px 8px; border-radius: 4px;">
                        <i class="fa-solid fa-mobile-screen"></i> <?= basename($v['video_mobile']) ?>
                      </span>
                    </td>
                    <td>
                      <?php if ($v['active'] ?? true): ?>
                        <span class="badge badge-confirmed">Active</span>
                      <?php else: ?>
                        <span class="badge badge-cancelled">Disabled</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <div style="display: flex; gap: 8px;">
                        <a href="banner_videos.php?toggle=<?= urlencode($v['id']) ?>" class="btn-admin" style="background: rgba(212,175,55,0.15); color: var(--admin-gold); font-size: 0.78rem; padding: 6px 12px;">
                          <?= ($v['active'] ?? true) ? 'Disable' : 'Enable' ?>
                        </a>
                        <a href="banner_videos.php?delete=<?= urlencode($v['id']) ?>" onclick="return confirm('Are you sure you want to remove this banner video?')" class="btn-admin btn-admin-delete">
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
