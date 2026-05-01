<div id="view-dashboard" class="spa-view">
    <div class="dashboard-stats">
        <div class="stat-card glass-panel">
            <div class="stat-icon purple"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg></div>
            <div class="stat-info">
                <h3>Active Projects</h3>
                <p class="stat-value">0</p>
            </div>
        </div>
        <div class="stat-card glass-panel">
            <div class="stat-icon blue"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg></div>
            <div class="stat-info">
                <h3>Pending Tasks</h3>
                <p class="stat-value">0</p>
            </div>
        </div>
        <div class="stat-card glass-panel">
            <div class="stat-icon green"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></div>
            <div class="stat-info">
                <h3>Completed</h3>
                <p class="stat-value">0</p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-column">
            <h3 class="section-title">Recent Projects</h3>
            <div class="project-list" id="dashboard-recent-projects">
                <p class="text-muted">Loading projects...</p>
            </div>
        </div>
        <div class="dashboard-column">
            <h3 class="section-title">My Tasks</h3>
            <div class="task-list">
                <label class="task-item">
                    <input type="checkbox" class="custom-checkbox">
                    <span class="task-title">Review final mockups</span>
                    <span class="task-date">Today</span>
                </label>
                <label class="task-item">
                    <input type="checkbox" class="custom-checkbox" checked>
                    <span class="task-title">Draft API endpoints</span>
                    <span class="task-date">Yesterday</span>
                </label>
            </div>
        </div>
    </div>
</div>
