const fs = require('fs');
const files = ['index.html', 'about.html', 'services.html', 'academy.html', 'salon.html', 'videos.html', 'gallery.html', 'contact.html'];
files.forEach(f => {
  if (fs.existsSync(f)) {
    const c = fs.readFileSync(f, 'utf8');
    if (c.includes('services-catalog-grid') || c.includes('service-detail-card')) {
      console.log('Found in', f);
      const lines = c.split('\n');
      lines.forEach((l, idx) => {
        if (l.includes('services-catalog-grid') || l.includes('service-detail-card')) {
          console.log(`  Line ${idx+1}: ${l.trim()}`);
        }
      });
    }
  }
});
