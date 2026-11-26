const fs = require('fs');
const files = ['index.html', 'about.html', 'services.html', 'academy.html', 'salon.html', 'videos.html', 'gallery.html', 'contact.html'];
files.forEach(f => {
  const c = fs.readFileSync(f, 'utf8');
  const m = c.match(/<div class="modal-backdrop" id="appointment-modal">[\s\S]*?<\/form>\s*<\/div>\s*<\/div>/);
  if (m) {
    console.log(f, 'matched modal length:', m[0].length);
  } else {
    console.log(f, 'NO MATCH');
  }
});
