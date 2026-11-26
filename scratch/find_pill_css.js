const fs = require('fs');
const c = fs.readFileSync('assets/css/style.css', 'utf8');
const lines = c.split('\n');
['dark-accent-card', 'salon-facilities-pills', 'facility-pill'].forEach(term => {
  console.log(`=== Matches for ${term} ===`);
  lines.forEach((l, idx) => {
    if (l.includes(term)) {
      console.log(`  Line ${idx+1}: ${l}`);
    }
  });
});
