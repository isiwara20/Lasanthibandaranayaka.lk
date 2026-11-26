const fs = require('fs');
const c = fs.readFileSync('index.html', 'utf8');
const lines = c.split('\n');
lines.forEach((l, idx) => {
  if (l.toLowerCase().includes('comfort') || l.toLowerCase().includes('promise') || l.toLowerCase().includes('facilities')) {
    console.log(`Line ${idx+1}: ${l.trim()}`);
  }
});
