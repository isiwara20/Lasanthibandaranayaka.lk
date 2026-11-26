const fs = require('fs');
const c = fs.readFileSync('assets/css/style.css', 'utf8');
const lines = c.split('\n');
lines.forEach((l, idx) => {
  if (l.includes('.modal-') && idx > 2350) {
    console.log((idx+1) + ': ' + l);
  }
});
