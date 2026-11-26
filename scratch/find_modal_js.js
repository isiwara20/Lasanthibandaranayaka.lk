const fs = require('fs');
const c = fs.readFileSync('assets/js/main.js', 'utf8');
const lines = c.split('\n');
lines.forEach((line, idx) => {
  if (line.includes('modal') || line.includes('appointment') || line.includes('lightbox')) {
    console.log((idx + 1) + ': ' + line);
  }
});
