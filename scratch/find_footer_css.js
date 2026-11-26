const fs = require('fs');
const c = fs.readFileSync('assets/css/style.css', 'utf8');
const lines = c.split('\n');
lines.forEach((l, idx) => {
  if (l.includes('footer-bottom-copy') || l.includes('footer-copy-text') || l.includes('footer-address-text') || l.includes('footer-staff-')) {
    console.log(`${idx+1}: ${l}`);
  }
});
