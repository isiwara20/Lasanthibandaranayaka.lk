const fs = require('fs');
const c = fs.readFileSync('admin/testimonials.php', 'utf8');
const lines = c.split('\n');
lines.forEach((l, idx) => {
  if (l.includes('<head>') || l.includes('<link') || l.includes('admin.css') || l.includes('style.css')) {
    console.log(`${idx+1}: ${l}`);
  }
});
