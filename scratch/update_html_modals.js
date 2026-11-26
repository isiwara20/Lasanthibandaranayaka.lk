const fs = require('fs');

// Check CSS braces
const css = fs.readFileSync('assets/css/style.css', 'utf8');
let openBraces = 0, closeBraces = 0;
for (let c of css) {
  if (c === '{') openBraces++;
  if (c === '}') closeBraces++;
}
console.log('CSS Braces: { =', openBraces, '} =', closeBraces, openBraces === closeBraces ? 'BALANCED!' : 'MISMATCH!');

// Files to update
const pages = [
  'academy.html',
  'index.html',
  'about.html',
  'services.html',
  'salon.html',
  'videos.html',
  'gallery.html',
  'contact.html'
];

pages.forEach(file => {
  if (!fs.existsSync(file)) return;
  let content = fs.readFileSync(file, 'utf8');

  if (file === 'academy.html') {
    // Replace academy header
    const target = /<div style="text-align: center;\s*margin-bottom: 24px;">\s*<span class="tag-label">Academy Registration<\/span>\s*<h2 style="[^"]*">Enroll in a Course<\/h2>\s*<p style="[^"]*">Connect with Lasanthi Salon Dambulla directly via WhatsApp<\/p>\s*<\/div>/;
    const replacement = `<div class="modal-header-wrap">
        <span class="tag-label">Academy Registration</span>
        <h2 class="modal-title">Enroll in a Course</h2>
        <p class="modal-subtitle">Connect with Lasanthi Salon Dambulla directly via WhatsApp</p>
      </div>`;
    if (target.test(content)) {
      content = content.replace(target, replacement);
      fs.writeFileSync(file, content, 'utf8');
      console.log('Updated', file, 'successfully');
    } else {
      console.log('Target not found in', file);
    }
  } else {
    // Other pages: Reservation / Book an Appointment
    const target = /<div style="text-align: center;\s*margin-bottom: 24px;">\s*<span class="tag-label">Reservation<\/span>\s*<h2 style="[^"]*">Book an Appointment<\/h2>\s*<p style="[^"]*">Connect with Lasanthi Salon Dambulla\s*directly via WhatsApp<\/p>\s*<\/div>/;
    const replacement = `<div class="modal-header-wrap">
        <span class="tag-label">Reservation</span>
        <h2 class="modal-title">Book an Appointment</h2>
        <p class="modal-subtitle">Connect with Lasanthi Salon Dambulla directly via WhatsApp</p>
      </div>`;
    if (target.test(content)) {
      content = content.replace(target, replacement);
      fs.writeFileSync(file, content, 'utf8');
      console.log('Updated', file, 'successfully');
    } else {
      console.log('Target not found in', file);
    }
  }
});
