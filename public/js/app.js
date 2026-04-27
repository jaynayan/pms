$(document).ready(function () {
    // Extract token from URL if present
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    if (token) {
        localStorage.setItem('auth_token', token);
        // Clean up URL
        window.history.replaceState({}, document.title, window.location.pathname + window.location.hash);
    }

    // Basic SPA Routing Logic
    function navigateTo(targetId) {
        // Update nav items
        $('.nav-item').removeClass('active');
        $(`.nav-item[data-target="${targetId}"]`).addClass('active');

        // Update page title
        const titles = {
            'dashboard': 'Dashboard',
            'projects': 'Projects Management',
            'backlog': 'Product Backlog',
            'board': 'Kanban Board'
        };
        $('#current-page-title').text(titles[targetId] || 'PMS');

        // This is a placeholder for actual AJAX content loading.
        // In a full SPA, we would load the view HTML and inject it into #app-content.
        console.log(`Navigated to ${targetId}`);

        // For visual feedback, we can add a subtle fade effect to the content area
        $('#app-content').css('opacity', 0);
        setTimeout(() => {
            $('#app-content').css('opacity', 1);
            $('#app-content').css('transition', 'opacity 0.3s ease');
        }, 50);
    }

    // Handle Nav Clicks
    $('.nav-item').on('click', function (e) {
        // e.preventDefault(); // Uncomment if preventing default hash change
        const target = $(this).data('target');
        if (target) {
            navigateTo(target);
        }
    });

    // Check hash on load to route properly
    const currentHash = window.location.hash.replace('#', '');
    if (currentHash) {
        navigateTo(currentHash);
    }
});
