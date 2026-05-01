// Global Toast Function
window.showToast = function (message, type = 'success') {
    const bg = type === 'success' ? '#10b981' : '#ef4444';
    const icon = type === 'success'
        ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
        : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';

    const toast = $(`
        <div class="toast" style="background: ${bg}; color: white; padding: 12px 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 10px; font-size: 0.9rem; font-weight: 500; transform: translateY(20px); opacity: 0; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
            ${icon}
            <span>${message}</span>
        </div>
    `);

    $('#toast-container').append(toast);

    setTimeout(() => {
        toast.css({ transform: 'translateY(0)', opacity: '1' });
    }, 10);

    setTimeout(() => {
        toast.css({ transform: 'translateY(20px)', opacity: '0' });
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

$(document).ready(function () {
    // Extract token from URL if present
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');
    if (token) {
        localStorage.setItem('auth_token', token);
        // Clean up URL
        window.history.replaceState({}, document.title, window.location.pathname + window.location.hash);
    }

    // Global AJAX Setup for Sanctum Auth
    $.ajaxSetup({
        headers: {
            'Authorization': 'Bearer ' + localStorage.getItem('auth_token'),
            'Accept': 'application/json'
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                // Unauthorized - token might be invalid or expired
                localStorage.removeItem('auth_token');
                window.location.reload();
            }
        }
    });

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

        // Hide all views, show the target view
        $('.spa-view').hide();
        $(`#view-${targetId}`).fadeIn(300);

        // View-specific logic
        if (targetId === 'projects') {
            if (typeof ProjectModule !== 'undefined') ProjectModule.loadProjects();
        } else if (targetId === 'dashboard') {
            if (typeof ProjectModule !== 'undefined') ProjectModule.loadDashboardProjects();
        } else if (targetId === 'backlog') {
            if (typeof TaskModule !== 'undefined') TaskModule.loadBacklog();
        } else if (targetId === 'board') {
            if (typeof TaskModule !== 'undefined') TaskModule.loadBoard();
        }
    }

    // Handle Nav Clicks
    $('.nav-item').on('click', function (e) {
        // e.preventDefault(); // Uncomment if preventing default hash change
        const target = $(this).data('target');
        if (target) {
            navigateTo(target);
        }
    });

    // Initial load route
    const currentHash = window.location.hash.replace('#', '');
    if (currentHash && $(`#view-${currentHash}`).length) {
        navigateTo(currentHash);
    } else if (localStorage.getItem('auth_token')) {
        navigateTo('dashboard');
    }

    // Initialize modules
    if (typeof ProjectModule !== 'undefined') {
        ProjectModule.init();
    }
    if (typeof TaskModule !== 'undefined') {
        TaskModule.init();
    }
});
