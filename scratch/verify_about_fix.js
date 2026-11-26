const fs = require('fs');

const css = fs.readFileSync('assets/css/style.css', 'utf8');
let openBraces = 0, closeBraces = 0;
for (let c of css) {
  if (c === '{') openBraces++;
  if (c === '}') closeBraces++;
}
console.log('CSS Braces: { =', openBraces, '} =', closeBraces, openBraces === closeBraces ? 'BALANCED!' : 'MISMATCH!');

// Verify about.html
const about = fs.readFileSync('about.html', 'utf8');
console.log('about.html has repeat(3, 1fr)?', about.includes('repeat(3, 1fr)'));
console.log('about.html has inline color #c9bfb2?', about.includes('color: #c9bfb2'));
