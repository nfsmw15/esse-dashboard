function toggleSidebar() {
    document.getElementById('dash-sidebar')?.classList.toggle('open');
    document.getElementById('sidebar-overlay')?.classList.toggle('show');
}

function toggleSubMenu(event, element) {
    event.preventDefault();
    element.classList.toggle('open');
}

document.querySelectorAll('[data-sidebar-toggle]').forEach(element => {
    element.addEventListener('click', toggleSidebar);
});

document.querySelectorAll('[data-submenu-toggle]').forEach(element => {
    element.addEventListener('click', event => toggleSubMenu(event, element.parentElement));
});

document.querySelectorAll('[data-history-back]').forEach(element => {
    element.addEventListener('click', event => {
        event.preventDefault();
        history.back();
    });
});

document.querySelectorAll('.dash-has-children').forEach(element => {
    if (element.querySelector('.dash-sub a.active')) {
        element.classList.add('open');
    }
});

(() => {
    const storedTheme = localStorage.getItem('esse-dashboard-theme') === 'dark' ? 'dark' : 'light';
    const setTheme = theme => {
        theme = theme === 'dark' ? 'dark' : 'light';
        localStorage.setItem('esse-dashboard-theme', theme);
        document.documentElement.setAttribute('data-bs-theme', theme);
        document.querySelectorAll('[data-bs-theme-value]').forEach(button => {
            button.classList.toggle('active', button.getAttribute('data-bs-theme-value') === theme);
        });
        const activeIcon = document.querySelector(`[data-bs-theme-value="${theme}"] i`);
        const themeIcon = document.querySelector('.theme-icon-active');
        if (activeIcon && themeIcon) {
            themeIcon.innerHTML = activeIcon.outerHTML;
        }
    };

    setTheme(storedTheme);
    document.querySelectorAll('[data-bs-theme-value]').forEach(button => {
        button.addEventListener('click', () => setTheme(button.getAttribute('data-bs-theme-value')));
    });
})();
