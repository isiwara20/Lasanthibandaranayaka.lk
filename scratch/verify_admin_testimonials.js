const fs = require('fs');

const content = fs.readFileSync('admin/testimonials.php', 'utf8');
const links = [...content.matchAll(/<link[^>]*rel=["']stylesheet["'][^>]*>/gi)].map(m => m[0]);
const scripts = [...content.matchAll(/<script[^>]*src=["']([^"']*)["'][^>]*>/gi)].map(m => m[1]);

console.log('Stylesheets in admin/testimonials.php:');
links.forEach(l => console.log('  ', l));

console.log('Scripts in admin/testimonials.php:');
scripts.forEach(s => console.log('  ', s));

// Check if assets/admin.css exists
console.log('Does admin/assets/admin.css exist?', fs.existsSync('admin/assets/admin.css'));
console.log('Does admin/assets/admin.js exist?', fs.existsSync('admin/assets/admin.js'));
