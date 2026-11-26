const fs = require('fs');
const c = fs.readFileSync('assets/css/style.css', 'utf8');
const lines = c.split('\n');
lines.forEach((line, idx) => {
  if (line.includes('lightbox')) {
    console.log((idx + 1) + ': ' + line);
  }
});
