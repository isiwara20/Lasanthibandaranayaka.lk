const fs = require('fs');
if (fs.existsSync('feedback.php')) {
  const c = fs.readFileSync('feedback.php', 'utf8');
  const m = c.match(/<footer[\s\S]*?<\/footer>/);
  if (m) console.log('feedback.php footer:\n', m[0]);
  else console.log('No footer tag in feedback.php');
}
