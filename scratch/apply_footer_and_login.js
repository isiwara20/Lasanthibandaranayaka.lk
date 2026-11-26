const fs = require('fs');

// 1. Update admin/login.php
let loginContent = fs.readFileSync('admin/login.php', 'utf8');
loginContent = loginContent.replace('placeholder="admin"', 'placeholder="Enter username"');
const defaultLoginRegex = /\s*<div style="margin-top: 24px; font-size: 0\.8rem; color: var\(--admin-muted\);">\s*Default Login: <code>admin<\/code> \/ <code>admin123<\/code>\s*<\/div>/;

if (defaultLoginRegex.test(loginContent)) {
  loginContent = loginContent.replace(defaultLoginRegex, '');
  fs.writeFileSync('admin/login.php', loginContent, 'utf8');
  console.log('Successfully removed default login display from admin/login.php');
} else {
  console.log('Warning: default login regex not matched in admin/login.php');
}

// 2. Update HTML pages footer
const pages = [
  'index.html',
  'about.html',
  'services.html',
  'academy.html',
  'salon.html',
  'videos.html',
  'gallery.html',
  'contact.html'
];

const targetPattern = /<div class="footer-copy-text">&copy; 2025 Lasanthi Bandaranayaka\. All Rights Reserved\.<\/div>/g;
const replacement = '<div class="footer-copy-text">&copy; 2026 Lasanthi Bandaranayaka. All Rights Reserved. &bull; Developed by <a href="https://www.facebook.com/chandilagroups/" target="_blank" rel="noopener" class="footer-dev-link">Chandila</a>.</div>';

pages.forEach(p => {
  if (fs.existsSync(p)) {
    let c = fs.readFileSync(p, 'utf8');
    if (targetPattern.test(c)) {
      c = c.replace(targetPattern, replacement);
      fs.writeFileSync(p, c, 'utf8');
      console.log(`Updated footer in ${p}`);
    } else {
      console.log(`Warning: Target not found in ${p}`);
    }
  }
});

// 3. Update assets/css/style.css to include footer-dev-link
let css = fs.readFileSync('assets/css/style.css', 'utf8');
if (!css.includes('.footer-dev-link')) {
  css = css.replace('.footer-copy-text {', `.footer-dev-link {
  color: var(--color-gold-400);
  text-decoration: none;
  font-weight: 600;
  transition: var(--transition);
}

.footer-dev-link:hover {
  color: var(--color-gold-300);
  text-decoration: underline;
}

.footer-copy-text {`);
  fs.writeFileSync('assets/css/style.css', css, 'utf8');
  console.log('Added .footer-dev-link to assets/css/style.css');
}
