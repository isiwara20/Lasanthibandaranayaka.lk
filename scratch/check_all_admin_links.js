const fs = require('fs');
const path = require('path');

const files = fs.readdirSync('admin').filter(f => f.endsWith('.php'));
files.forEach(f => {
  const content = fs.readFileSync(path.join('admin', f), 'utf8');
  const links = [...content.matchAll(/<link[^>]*rel=["']stylesheet["'][^>]*>/gi)].map(m => m[0]);
  console.log(`=== admin/${f} ===`);
  links.forEach(l => console.log('  ', l));
});
