const fs = require('fs');

const css = fs.readFileSync('assets/css/style.css', 'utf8');
let openBraces = 0, closeBraces = 0;
for (let c of css) {
  if (c === '{') openBraces++;
  if (c === '}') closeBraces++;
}
console.log('CSS Braces: { =', openBraces, '} =', closeBraces, openBraces === closeBraces ? 'BALANCED!' : 'MISMATCH!');

// Verify salon.html has no more inline color white/c4b9ac
const salon = fs.readFileSync('salon.html', 'utf8');
console.log('salon.html has inline color #fff?', salon.includes('color: #fff'));
console.log('salon.html has inline color #c4b9ac?', salon.includes('color: #c4b9ac'));
