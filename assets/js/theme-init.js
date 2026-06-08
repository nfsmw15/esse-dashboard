(() => {
    const storedTheme = localStorage.getItem('esse-dashboard-theme');
    document.documentElement.setAttribute('data-bs-theme', storedTheme === 'dark' ? 'dark' : 'light');
})();
