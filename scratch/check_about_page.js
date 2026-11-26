const fs = require('fs');
const c = fs.readFileSync('about.html', 'utf8');
const lines = c.split('\n');
lines.forEach((l, idx) => {
  if (l.includes('about-title') || l.includes('about-text')) {
    console.log(`Line ${idx+1}: ${l.trim()}`);
  }
});
