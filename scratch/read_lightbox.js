const fs = require('fs');
const c = fs.readFileSync('index.html', 'utf8');
const m = c.match(/<div[^>]*id="lightbox-modal"[\s\S]*?<\/div>\s*<\/div>/);
if (m) console.log(m[0]);
