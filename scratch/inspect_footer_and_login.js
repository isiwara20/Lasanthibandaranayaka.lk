const fs = require('fs');
const path = require('path');

// Check login.php
const loginContent = fs.readFileSync('admin/login.php', 'utf8');
console.log('=== admin/login.php ===');
const loginLines = loginContent.split('\n');
loginLines.forEach((l, idx) => {
  if (l.toLowerCase().includes('default') || l.toLowerCase().includes('demo') || l.toLowerCase().includes('admin') || l.toLowerCase().includes('credential')) {
    console.log(`  Line ${idx+1}: ${l.trim()}`);
  }
});

// Check footer copyright in files
const pages = ['index.html', 'about.html', 'services.html', 'academy.html', 'salon.html', 'videos.html', 'gallery.html', 'contact.html', 'feedback.php'];
console.log('=== Footer in pages ===');
pages.forEach(p => {
  if (fs.existsSync(p)) {
    const c = fs.readFileSync(p, 'utf8');
    const m = c.match(/<div class="footer-bottom-copy"[\s\S]*?<\/footer>/);
    if (m) {
      console.log(`-- ${p} --\n` + m[0]);
    }
  }
});
