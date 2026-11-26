const fs = require('fs');
const path = require('path');

function walk(dir) {
  let results = [];
  const list = fs.readdirSync(dir);
  list.forEach(file => {
    if (file === 'node_modules' || file === '.git') return;
    const fullPath = path.join(dir, file);
    const stat = fs.statSync(fullPath);
    if (stat && stat.isDirectory()) {
      results = results.concat(walk(fullPath));
    } else {
      results.push(fullPath);
    }
  });
  return results;
}

const allFiles = walk('.');
allFiles.forEach(file => {
  if (file.endsWith('.php') || file.endsWith('.html')) {
    const c = fs.readFileSync(file, 'utf8');
    if (c.includes('All Rights Reserved') || c.includes('2025')) {
      console.log('Match in:', file);
    }
  }
});
