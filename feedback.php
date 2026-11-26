<?php
$message = '';
$error = '';
$submitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? 'Valued Client');
    $location = trim($_POST['location'] ?? 'Sri Lanka');
    $service = trim($_POST['service'] ?? 'Salon Service');
    $rating = intval($_POST['rating'] ?? 5);
    $quote = trim($_POST['quote'] ?? '');
    $avatar = 'assets/images/client_nimali.jpg'; // default avatar

    if ($rating < 1) $rating = 1;
    if ($rating > 5) $rating = 5;

    // Handle optional photo upload
    if (!empty($_FILES['photo']['name'])) {
        $file = $_FILES['photo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed) && $file['size'] <= 10 * 1024 * 1024) {
            $destDir = __DIR__ . '/uploads/testimonials/';
            if (!is_dir($destDir)) {
                mkdir($destDir, 0777, true);
            }
            $filename = 'client_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $dest = $destDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $avatar = 'uploads/testimonials/' . $filename;
            }
        }
    }

    if (empty($name)) {
        $error = 'Please enter your name.';
    } elseif (empty($quote)) {
        $error = 'Please share a few words about your experience.';
    } else {
        $dataFile = __DIR__ . '/data/testimonials.json';
        $items = [];
        if (file_exists($dataFile)) {
            $items = json_decode(file_get_contents($dataFile), true) ?: [];
        }

        $newReview = [
            'id' => 'test_' . time(),
            'name' => htmlspecialchars($name),
            'role' => htmlspecialchars($role),
            'location' => htmlspecialchars($location),
            'service' => htmlspecialchars($service),
            'rating' => $rating,
            'quote' => htmlspecialchars($quote),
            'avatar' => $avatar,
            'status' => 'pending', // Requires staff approval
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Prepend so latest appears first
        array_unshift($items, $newReview);
        file_put_contents($dataFile, json_encode($items, JSON_PRETTY_PRINT));
        $submitted = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Share Your Experience | Lasanthi Bandaranayaka Salon & Academy</title>
  <link rel="icon" type="image/png" href="assets/images/logo_gold.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800&family=Great+Vibes&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .feedback-page-wrapper {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px 16px 60px;
      position: relative;
      background: radial-gradient(circle at 50% 15%, rgba(212, 175, 55, 0.12) 0%, rgba(12, 10, 8, 0.98) 75%);
    }

    .feedback-card {
      width: 100%;
      max-width: 640px;
      background: rgba(22, 18, 14, 0.94);
      border: 1px solid rgba(212, 175, 55, 0.4);
      border-radius: 24px;
      padding: 36px 32px;
      box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7), 0 0 35px rgba(212, 175, 55, 0.15);
      backdrop-filter: blur(16px);
      box-sizing: border-box;
      position: relative;
    }

    .feedback-header {
      text-align: center;
      margin-bottom: 28px;
    }

    .feedback-logo {
      height: 65px;
      margin-bottom: 12px;
      filter: drop-shadow(0 4px 15px rgba(212, 175, 55, 0.3));
    }

    .feedback-title {
      font-family: var(--font-serif);
      font-size: 1.85rem;
      color: #fff;
      margin-bottom: 8px;
    }

    .feedback-sub {
      color: #bfae9c;
      font-size: 0.95rem;
      line-height: 1.5;
    }

    .star-rating-group {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin: 16px 0 22px;
      flex-direction: row-reverse;
    }

    .star-rating-group input {
      display: none;
    }

    .star-rating-group label {
      font-size: 2.2rem;
      color: rgba(255, 255, 255, 0.2);
      cursor: pointer;
      transition: all 0.25s ease;
    }

    .star-rating-group input:checked ~ label,
    .star-rating-group label:hover,
    .star-rating-group label:hover ~ label {
      color: #f59e0b;
      transform: scale(1.15);
      text-shadow: 0 0 15px rgba(245, 158, 11, 0.5);
    }

    .rating-caption {
      text-align: center;
      font-size: 0.85rem;
      color: var(--color-gold-300);
      font-weight: 600;
      letter-spacing: 0.5px;
      margin-top: -10px;
      margin-bottom: 22px;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
      margin-bottom: 16px;
    }

    .form-group {
      margin-bottom: 16px;
    }

    .form-label {
      display: block;
      font-size: 0.84rem;
      font-weight: 600;
      color: #e5ded5;
      margin-bottom: 6px;
      letter-spacing: 0.3px;
    }

    .form-control {
      width: 100%;
      background: rgba(10, 8, 6, 0.7);
      border: 1px solid rgba(212, 175, 55, 0.35);
      border-radius: 12px;
      padding: 12px 14px;
      color: #fff;
      font-size: 0.92rem;
      box-sizing: border-box;
      transition: all 0.3s ease;
      font-family: inherit;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--color-gold-300);
      box-shadow: 0 0 15px rgba(212, 175, 55, 0.25);
      background: rgba(18, 14, 10, 0.9);
    }

    textarea.form-control {
      min-height: 110px;
      resize: vertical;
    }

    .file-upload-box {
      border: 1px dashed rgba(212, 175, 55, 0.4);
      border-radius: 12px;
      padding: 14px;
      text-align: center;
      cursor: pointer;
      background: rgba(212, 175, 55, 0.03);
      transition: all 0.3s;
    }

    .file-upload-box:hover {
      background: rgba(212, 175, 55, 0.08);
      border-color: var(--color-gold-300);
    }

    .file-upload-box input {
      display: none;
    }

    .file-upload-box label {
      cursor: pointer;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
      font-size: 0.85rem;
      color: #c9bdae;
    }

    .file-upload-box i {
      font-size: 1.4rem;
      color: var(--color-gold-300);
    }

    .submit-btn {
      width: 100%;
      background: var(--gold-gradient);
      color: #120e08;
      font-weight: 700;
      font-size: 1rem;
      border: none;
      border-radius: 30px;
      padding: 14px;
      cursor: pointer;
      box-shadow: 0 6px 20px rgba(212, 175, 55, 0.35);
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-top: 24px;
    }

    .submit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(212, 175, 55, 0.5);
    }

    .success-card {
      text-align: center;
      padding: 20px 10px;
    }

    .success-icon {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: rgba(39, 174, 96, 0.15);
      border: 2px solid #27ae60;
      color: #2ecc71;
      font-size: 2rem;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 20px;
    }

    .success-title {
      font-family: var(--font-serif);
      font-size: 1.75rem;
      color: #fff;
      margin-bottom: 12px;
    }

    .success-desc {
      color: #c9bfb2;
      font-size: 0.98rem;
      line-height: 1.7;
      margin-bottom: 26px;
    }

    .whatsapp-share-pill {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: #25d366;
      color: #fff;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.95rem;
      padding: 12px 24px;
      border-radius: 25px;
      box-shadow: 0 4px 15px rgba(37, 211, 102, 0.35);
      transition: all 0.3s ease;
    }

    .whatsapp-share-pill:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5);
    }

    @media (max-width: 600px) {
      .feedback-card {
        padding: 26px 18px;
        border-radius: 18px;
      }
      .form-row {
        grid-template-columns: 1fr;
        gap: 0;
      }
      .feedback-title {
        font-size: 1.55rem;
      }
    }

    /* Light Theme Support */
    [data-theme="light"] .feedback-page-wrapper {
      background: radial-gradient(circle at 50% 15%, rgba(212, 175, 55, 0.18) 0%, #faf6f0 75%);
    }
    [data-theme="light"] .feedback-card {
      background: #ffffff;
      border-color: rgba(212, 175, 55, 0.45);
      box-shadow: 0 20px 50px rgba(212, 175, 55, 0.15);
    }
    [data-theme="light"] .feedback-title {
      color: #1a1510;
    }
    [data-theme="light"] .feedback-sub {
      color: #5c5042;
    }
    [data-theme="light"] .form-label {
      color: #2b2218;
    }
    [data-theme="light"] .form-control {
      background: #faf7f2;
      border-color: rgba(212, 175, 55, 0.35);
      color: #1a1510;
    }
    [data-theme="light"] .form-control:focus {
      background: #ffffff;
    }
    [data-theme="light"] .success-title {
      color: #1a1510;
    }
    [data-theme="light"] .success-desc {
      color: #4a3e30;
    }
  </style>
</head>
<body>
  <div class="feedback-page-wrapper">
    <div class="feedback-card">
      <div class="feedback-header">
        <a href="index.html">
          <img src="assets/images/logo_transparent.png" alt="Lasanthi Salon & Academy" class="feedback-logo">
        </a>
        <h1 class="feedback-title">Share Your Experience</h1>
        <p class="feedback-sub">Your words inspire our artistry and help prospective brides make their royal wedding dreams a reality.</p>
      </div>

      <?php if (!empty($error)): ?>
        <div style="background: rgba(231, 76, 60, 0.15); border: 1px solid #e74c3c; color: #ff6b6b; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 0.9rem;">
          <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <?php if ($submitted): ?>
        <div class="success-card">
          <div class="success-icon">
            <i class="fa-solid fa-check"></i>
          </div>
          <h2 class="success-title">Thank You, <?= htmlspecialchars($name) ?>!</h2>
          <p class="success-desc">
            Your heartfelt review has been submitted with gratitude. Our salon team will review your story, and once approved, it will be proudly featured on our official website testimonials.
          </p>

          <div style="display: flex; flex-direction: column; gap: 12px; align-items: center;">
            <a href="https://wa.me/94777958184?text=Hi%20Lasanthi%20Salon,%20I%20just%20submitted%20my%20review%20as%20<?= urlencode($name) ?>!" target="_blank" rel="noopener" class="whatsapp-share-pill">
              <i class="fa-brands fa-whatsapp" style="font-size: 1.2rem;"></i>
              <span>Send Note to Lasanthi on WhatsApp</span>
            </a>

            <a href="index.html" class="btn-outline-gold" style="padding: 10px 22px; font-size: 0.9rem; margin-top: 10px;">
              <i class="fa-solid fa-arrow-left"></i>
              <span>Return to Official Website</span>
            </a>
          </div>
        </div>
      <?php else: ?>
        <form method="POST" action="feedback.php" enctype="multipart/form-data">
          <!-- Star Rating -->
          <div style="text-align: center;">
            <span class="form-label">How would you rate your experience?</span>
            <div class="star-rating-group">
              <input type="radio" id="star5" name="rating" value="5" checked onclick="updateRatingCaption('5 Stars - Exceptional Royal Experience')">
              <label for="star5" title="5 Stars - Exceptional"><i class="fa-solid fa-star"></i></label>
              <input type="radio" id="star4" name="rating" value="4" onclick="updateRatingCaption('4 Stars - Very Good Experience')">
              <label for="star4" title="4 Stars - Very Good"><i class="fa-solid fa-star"></i></label>
              <input type="radio" id="star3" name="rating" value="3" onclick="updateRatingCaption('3 Stars - Good Service')">
              <label for="star3" title="3 Stars - Good"><i class="fa-solid fa-star"></i></label>
              <input type="radio" id="star2" name="rating" value="2" onclick="updateRatingCaption('2 Stars - Fair')">
              <label for="star2" title="2 Stars - Fair"><i class="fa-solid fa-star"></i></label>
              <input type="radio" id="star1" name="rating" value="1" onclick="updateRatingCaption('1 Star - Needs Improvement')">
              <label for="star1" title="1 Star - Poor"><i class="fa-solid fa-star"></i></label>
            </div>
            <div class="rating-caption" id="rating-caption">5 Stars - Exceptional Royal Experience</div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="client-name">Your Full Name *</label>
              <input type="text" id="client-name" name="name" class="form-control" placeholder="e.g. Nimali Perera" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label class="form-label" for="client-role">Role / Occasion</label>
              <select id="client-role" name="role" class="form-control">
                <option value="Kandyan Bride">Kandyan Bride</option>
                <option value="Western Bride">Western Bride</option>
                <option value="Going Away Bride">Going Away Bride</option>
                <option value="Bridal Entourage / Retinue">Bridal Entourage / Retinue</option>
                <option value="Salon Hair & Beauty Client">Salon Hair & Beauty Client</option>
                <option value="Skincare & Spa Client">Skincare & Spa Client</option>
                <option value="Academy Student / Graduate">Academy Student / Graduate</option>
                <option value="Valued Client">Valued Client</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="client-service">Service Received</label>
              <select id="client-service" name="service" class="form-control">
                <option value="Bridal Dressing">Bridal Dressing & Regalia</option>
                <option value="Hair Styling & Color">Hair Styling, Color & Spa</option>
                <option value="Facial & Skincare">Facial, Hydra-Glow & Skin Therapy</option>
                <option value="Makeup Artistry">Professional Makeup Artistry</option>
                <option value="Nail Art & Care">Nail Art & Luxury Pedicure</option>
                <option value="Academy Diploma Course">Beauty Academy Diploma Course</option>
                <option value="Full Salon Package">Full Salon Beauty Package</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label" for="client-location">Your City / Hometown</label>
              <input type="text" id="client-location" name="location" class="form-control" placeholder="e.g. Dambulla, Kandy, Colombo" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="client-quote">Your Review & Story *</label>
            <textarea id="client-quote" name="quote" class="form-control" placeholder="Tell us about your bridal look, the salon ambiance, Lasanthi's craftsmanship, or how you felt on your big day..." required><?= htmlspecialchars($_POST['quote'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Upload Your Photo (Optional)</label>
            <div class="file-upload-box" onclick="document.getElementById('client-photo').click()">
              <input type="file" id="client-photo" name="photo" accept="image/*" onchange="previewUploadedPhoto(this)">
              <label for="client-photo" id="photo-label">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span id="photo-text">Upload a picture of your bridal look or portrait (JPG, PNG, WebP)</span>
              </label>
            </div>
          </div>

          <button type="submit" class="submit-btn">
            <i class="fa-solid fa-paper-plane"></i>
            <span>Submit Your Review</span>
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function updateRatingCaption(text) {
      document.getElementById('rating-caption').textContent = text;
    }

    function previewUploadedPhoto(input) {
      if (input.files && input.files[0]) {
        const filename = input.files[0].name;
        document.getElementById('photo-text').textContent = 'Selected: ' + filename;
        document.getElementById('photo-text').style.color = '#f59e0b';
      }
    }
  </script>
</body>
</html>
