const fs = require('fs');
const files = ['index.html', 'about.html', 'services.html', 'academy.html', 'salon.html', 'videos.html', 'gallery.html', 'contact.html'];
files.forEach(f => {
  if (fs.existsSync(f)) {
    const c = fs.readFileSync(f, 'utf8');
    const m = c.match(/<div class="modal-backdrop"[\s\S]*?<\/form>/);
    if (m) {
      console.log('=== ' + f + ' ===');
      const header = m[0].match(/<div style="text-align: center[\s\S]*?<\/div>/);
      if (header) console.log(header[0]);
    }
  }
});
