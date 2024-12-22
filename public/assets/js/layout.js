// layout.js
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const header = document.querySelector('header');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const menuLinks = document.querySelectorAll('.sidebar-menu a');

    function toggleSidebar(show) {
        if (show) {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('collapsed');
            header.classList.remove('collapsed');
            document.body.classList.remove('sidebar-collapsed');
        } else {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('collapsed');
            header.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
        }
    }

    // Toggle button click handler
    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        sidebar.classList.contains('collapsed') ? toggleSidebar(true) : toggleSidebar(false);
    });

    // Auto close sidebar on mobile
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                toggleSidebar(false);
            }
        });
    });

    // Close sidebar when clicking outside
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 768 && 
            !sidebar.contains(e.target) && 
            !toggleBtn.contains(e.target)) {
            toggleSidebar(false);
        }
    });

    // Handle window resize
    function handleResize() {
        if (window.innerWidth <= 768) {
            toggleSidebar(false);
        } else {
            toggleSidebar(true);
        }
    }

    // Initial setup
    window.addEventListener('resize', handleResize);
    handleResize();
});