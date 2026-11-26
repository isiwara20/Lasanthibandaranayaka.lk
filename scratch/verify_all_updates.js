const fs = require('fs');
const { execSync } = require('child_process');

// 1. Verify CSS braces
const css = fs.readFileSync('assets/css/style.css', 'utf8');
let openB = 0, closeB = 0;
for (let c of css) {
  if (c === '{') openB++;
  if (c === '}') closeB++;
}
console.log(`CSS Braces: { = ${openB}, } = ${closeB} -> ${openB === closeB ? 'BALANCED' : 'MISMATCH'}`);

// 2. Verify admin/login.php
const login = fs.readFileSync('admin/login.php', 'utf8');
console.log('admin/login.php has Default Login?', login.includes('Default Login'));
console.log('admin/login.php has admin123?', login.includes('admin123'));
console.log('admin/login.php placeholder admin?', login.includes('placeholder="admin"'));

// 3. Verify footer in all 8 pages
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

pages.forEach(p => {
  const c = fs.readFileSync(p, 'utf8');
  const has2026 = c.includes('2026 Lasanthi Bandaranayaka');
  const hasChandila = c.includes('https://www.facebook.com/chandilagroups/');
  console.log(`${p}: has 2026? ${has2026}, has Chandila link? ${hasChandila}`);
});

// 4. PHP lint
try {
  const phpOut = execSync('php -l admin/login.php').toString();
  console.log('PHP Lint:', phpOut.trim());
} catch (e) {
  console.error('PHP Lint Error:', e.message);
}
