const fs = require('fs');
['index.html', 'about.html', 'services.html', 'academy.html', 'salon.html', 'videos.html', 'gallery.html', 'contact.html', 'feedback.php'].forEach(f => {
  if (fs.existsSync(f)) {
    const c = fs.readFileSync(f, 'utf8');
    const modals = [...c.matchAll(/id=["']([^"']*modal[^"']*)["']/gi)].map(m => m[1]);
    const forms = [...c.matchAll(/<form[^>]*id=["']([^"']*)["']/gi)].map(m => m[1]);
    console.log(f, '-> modals:', modals, 'forms:', forms);
  }
});
