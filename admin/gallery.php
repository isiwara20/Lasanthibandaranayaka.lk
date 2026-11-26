<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

$dataFile = __DIR__ . '/../data/gallery.json';
$gallery = json_decode(@file_get_contents($dataFile), true) ?: [];

$message = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $delId = $_GET['delete'];
    $gallery = array_values(array_filter($gallery, fn($g) => $g['id'] !== $delId));
    file_put_contents($dataFile, json_encode($gallery, JSON_PRETTY_PRINT));
    header('Location: gallery.php?msg=deleted');
    exit;
}

// Handle Upload New Image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_photo') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'Kandyan Bridal');
    
    if (empty($title)) {
        $error = 'Please enter a title for the photo.';
    } elseif (empty($_FILES['photo_file']['name'])) {
        $error = 'Please select an image file to upload.';
    } else {
        $file = $_FILES['photo_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            $error = 'Invalid image format. Allowed formats: JPG, PNG, WEBP.';
        } else {
            $filename = 'gal_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $destination = __DIR__ . '/../uploads/gallery/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $newImage = [
                    'id' => 'gal_' . time(),
                    'title' => htmlspecialchars($title),
                    'category' => htmlspecialchars($category),
                    'image_url' => 'uploads/gallery/' . $filename,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                array_unshift($gallery, $newImage);
                file_put_contents($dataFile, json_encode($gallery, JSON_PRETTY_PRINT));
                $message = 'Photo uploaded and added to gallery successfully!';
            } else {
                $error = 'Failed to upload image file. Please check folder permissions.';
            }
        }
    }
}

// Category filter
$catFilter = $_GET['cat'] ?? 'all';
$filteredGallery = $gallery;
if ($catFilter !== 'all') {
    $filteredGallery = array_filter($gallery, fn($g) => ($g['category'] ?? '') === $catFilter);
}

$categories = ['Kandyan Bridal', 'Western Bridal', 'Hair Artistry', 'Makeup Artistry', 'The Salon', 'Academy', 'Real Weddings'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gallery Manager | Lasanthi Salon Admin</title>
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
        <li class="nav-item">
          <a href="inquiries.php"><i class="fa-solid fa-envelope"></i> <span>Contact Inquiries</span></a>
        </li>
        <li class="nav-item active">
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
          <h1 class="admin-page-title">Gallery Photos Manager</h1>
          <p class="admin-page-sub">Upload, tag, and organize bridal, salon, and academy portfolio photos displayed on the website.</p>
        </div>
      </div>

      <?php if (!empty($message)): ?>
        <div class="alert-box alert-success"><i class="fa-solid fa-check"></i> <?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="alert-box alert-error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <!-- Upload New Photo Card -->
      <div class="admin-card">
        <div class="card-head">
          <h2 class="card-title"><i class="fa-solid fa-cloud-arrow-up text-gold"></i> Add New Photo to Gallery</h2>
        </div>

        <form method="POST" action="gallery.php" enctype="multipart/form-data">
          <input type="hidden" name="action" value="add_photo">

          <div class="form-grid">
            <div class="admin-form-group">
              <label class="admin-label" for="title">Photo Title / Caption</label>
              <input type="text" id="title" name="title" class="admin-input" placeholder="e.g. Royal Kandyan Bride Front Portrait" required>
            </div>

            <div class="admin-form-group">
              <label class="admin-label" for="category">Category</label>
              <select id="category" name="category" class="admin-select" required>
                <?php foreach ($categories as $c): ?>
                  <option value="<?= $c ?>"><?= $c ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="admin-form-group">
            <label class="admin-label">Select Image File (JPG, PNG, WEBP)</label>
            <input type="file" name="photo_file" accept="image/*" class="admin-input" style="padding: 10px;" required>
          </div>

          <button type="submit" class="btn-admin btn-admin-gold">
            <i class="fa-solid fa-upload"></i>
            <span>Upload & Add to Gallery</span>
          </button>
        </form>
      </div>

      <!-- Filter Tabs -->
      <div style="display: flex; gap: 8px; margin-bottom: 24px; flex-wrap: wrap;">
        <a href="gallery.php?cat=all" class="btn-admin <?= $catFilter === 'all' ? 'btn-admin-gold' : '' ?>" style="<?= $catFilter !== 'all' ? 'background: #18130f; border: 1px solid var(--admin-border); color: #c4baac;' : '' ?>">All Photos (<?= count($gallery) ?>)</a>
        <?php foreach ($categories as $c): ?>
          <a href="gallery.php?cat=<?= urlencode($c) ?>" class="btn-admin <?= $catFilter === $c ? 'btn-admin-gold' : '' ?>" style="<?= $catFilter !== $c ? 'background: #18130f; border: 1px solid var(--admin-border); color: #c4baac;' : '' ?>"><?= $c ?></a>
        <?php endforeach; ?>
      </div>

      <!-- Gallery Grid -->
      <div class="admin-gallery-grid">
        <?php if (empty($filteredGallery)): ?>
          <div style="grid-column: 1 / -1; padding: 40px; text-align: center; color: var(--admin-muted);">No photos found in this category.</div>
        <?php else: ?>
          <?php foreach ($filteredGallery as $g): ?>
            <div class="admin-gallery-item">
              <img src="../<?= htmlspecialchars($g['image_url']) ?>" alt="<?= htmlspecialchars($g['title']) ?>" loading="lazy">
              <div class="admin-gallery-overlay">
                <span class="badge" style="background: rgba(212,175,55,0.9); color: #000; align-self: flex-start; font-weight: 700;">
                  <?= htmlspecialchars($g['category']) ?>
                </span>
                <div>
                  <div style="color: #fff; font-size: 0.9rem; font-weight: 600; margin-bottom: 8px;"><?= htmlspecialchars($g['title']) ?></div>
                  <a href="gallery.php?delete=<?= urlencode($g['id']) ?>" onclick="return confirm('Remove this image from gallery?')" class="btn-admin btn-admin-delete" style="padding: 4px 10px; font-size: 0.75rem;">
                    <i class="fa-solid fa-trash"></i> Delete
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </main>
  </div>
  <script src="assets/admin.js"></script>
</body>
</html>

