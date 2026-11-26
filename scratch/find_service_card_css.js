const fs = require('fs');
const c = fs.readFileSync('assets/css/style.css', 'utf8');
const lines = c.split('\n');
['services-catalog-grid', 'service-detail-card', 'service-card-'].forEach(term => {
  console.log(`=== Matches for ${term} ===`);
  lines.forEach((l, idx) => {
    if (l.includes(term)) {
      console.log(`  Line ${idx+1}: ${l}`);
    }
  });
});
