const fs = require('fs');
const files = ['index.html', 'about.html', 'services.html', 'academy.html', 'salon.html', 'videos.html', 'gallery.html', 'contact.html'];
files.forEach(f => {
  const c = fs.readFileSync(f, 'utf8');
  const lines = c.split('\n');
  lines.forEach((l, idx) => {
    if (l.includes('style="color: #fff') || l.includes('style="color: #c4b9ac') || l.includes("style='color: #fff")) {
      console.log(`${f}:${idx+1} -> ${l.trim()}`);
    }
  });
});
