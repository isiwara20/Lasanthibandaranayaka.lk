const fs = require('fs');
const path = require('path');

const files = fs.readdirSync('admin').filter(f => f.endsWith('.php'));
files.forEach(f => {
  const content = fs.readFileSync(path.join('admin', f), 'utf8');
  const scripts = [...content.matchAll(/<script[^>]*src=["']([^"']*)["'][^>]*>/gi)].map(m => m[1]);
  console.log(`=== admin/${f} ===`);
  scripts.forEach(s => console.log('  ', s));
});
