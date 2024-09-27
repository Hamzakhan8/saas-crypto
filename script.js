// JavaScript to handle sidebar toggle
document.getElementById('sidebarToggle').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    const content = document.querySelector('.content');
    const toggleIcon = document.getElementById('toggleIcon');

    // Toggle the 'closed' class on sidebar
    sidebar.classList.toggle('closed');
    // Toggle the 'collapsed' class on content
    content.classList.toggle('collapsed');
    // Toggle the icon based on sidebar's class
    toggleIcon.textContent = sidebar.classList.contains('closed') ? '☰' : '✖';
});

document.addEventListener('DOMContentLoaded', function() {
    // ... existing code ...

    const analyticsToggle = document.querySelector('.nav-link[data-bs-toggle="collapse"]');
    const analyticsSubmenu = document.getElementById('analyticsSubmenu');

    analyticsToggle.addEventListener('click', function(e) {
        e.preventDefault();
        analyticsSubmenu.classList.toggle('show');
        this.setAttribute('aria-expanded', this.getAttribute('aria-expanded') === 'true' ? 'false' : 'true');
    });
});
