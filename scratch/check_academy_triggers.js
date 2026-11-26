const fs = require('fs');
const c = fs.readFileSync('academy.html', 'utf8');
const lines = c.split('\n');
lines.forEach((l, idx) => {
  if (l.includes('appointment-modal') || l.includes('open-modal') || l.includes('Enroll') || l.includes('Register')) {
    console.log((idx+1) + ': ' + l.trim());
  }
});
