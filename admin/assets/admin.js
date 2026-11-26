document.addEventListener('DOMContentLoaded', () => {
  const adminThemeToggle = document.getElementById('admin-theme-toggle');
  const applyTheme = (theme) => {
    document.documentElement.setAttribute('data-theme', theme);
    document.body.setAttribute('data-theme', theme);
    localStorage.setItem('admin_theme', theme);
  };

  const savedTheme = localStorage.getItem('admin_theme') || 'dark';
  applyTheme(savedTheme);

  if (adminThemeToggle) {
    adminThemeToggle.addEventListener('click', () => {
      const cur = document.documentElement.getAttribute('data-theme') || 'dark';
      const n = cur === 'dark' ? 'light' : 'dark';
      applyTheme(n);
    });
  }
});
