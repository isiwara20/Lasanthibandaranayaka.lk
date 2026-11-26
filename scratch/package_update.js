const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const filesToCopy = [
  'index.html',
  'about.html',
  'services.html',
  'academy.html',
  'salon.html',
  'videos.html',
  'gallery.html',
  'contact.html',
  'feedback.php',
  'assets/css/style.css',
  'assets/js/main.js',
  'admin/login.php',
  'admin/testimonials.php',
  'data/testimonials.json',
  'api/get_testimonials.php',
  'assets/images/academy_training.jpg',
  'assets/images/client_sanduni.jpg'
];

// Banner images
const imagesDir = path.join('assets', 'images');
if (fs.existsSync(imagesDir)) {
  const allImages = fs.readdirSync(imagesDir);
  allImages.forEach(img => {
    if (img.startsWith('banner_') && img.endsWith('.jpg')) {
      filesToCopy.push(path.join('assets', 'images', img));
    }
  });
}

const destDir = 'dist_update';
if (fs.existsSync(destDir)) {
  fs.rmSync(destDir, { recursive: true, force: true });
}
fs.mkdirSync(destDir, { recursive: true });

console.log('Copying files into dist_update/:');
filesToCopy.forEach(f => {
  const normalized = f.replace(/\\/g, '/');
  if (fs.existsSync(normalized)) {
    const destPath = path.join(destDir, normalized);
    const parent = path.dirname(destPath);
    if (!fs.existsSync(parent)) {
      fs.mkdirSync(parent, { recursive: true });
    }
    fs.copyFileSync(normalized, destPath);
    console.log('  -> ' + normalized);
  } else {
    console.log('  File not found: ' + normalized);
  }
});

// Create zip using PowerShell Compress-Archive
const zipPath = path.resolve('update_files.zip');
if (fs.existsSync(zipPath)) {
  fs.unlinkSync(zipPath);
}

try {
  execSync(`powershell -NoProfile -Command "Compress-Archive -Path '${destDir}\\*' -DestinationPath '${zipPath}' -Force"`);
  console.log('Successfully created update_files.zip at:', zipPath);
  const stat = fs.statSync(zipPath);
  console.log(`Zip size: ${(stat.size / 1024 / 1024).toFixed(2)} MB`);
} catch (err) {
  console.error('Error creating zip:', err.message);
}
