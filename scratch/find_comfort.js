const fs = require('fs');
const files = ['index.html', 'about.html', 'services.html', 'academy.html', 'salon.html', 'videos.html', 'gallery.html', 'contact.html'];
files.forEach(f => {
  if (fs.existsSync(f)) {
    const c = fs.readFileSync(f, 'utf8');
    if (c.toLowerCase().includes('comfort') || c.toLowerCase().includes('promises')) {
      console.log('Found in', f);
      const lines = c.split('\n');
      lines.forEach((l, idx) => {
        if (l.toLowerCase().includes('comfort') || l.toLowerCase().includes('promises')) {
          console.log(`  Line ${idx+1}: ${l.trim()}`);
        }
      });
    }
  }
});
