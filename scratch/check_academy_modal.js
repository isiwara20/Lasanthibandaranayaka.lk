const fs = require('fs');
const c = fs.readFileSync('academy.html', 'utf8');
const lines = c.split('\n');
console.log(lines.slice(215, 245).join('\n'));
