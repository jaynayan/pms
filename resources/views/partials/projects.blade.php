<div id="view-projects" class="spa-view" style="display: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 class="section-title" style="margin: 0;">All Projects</h3>
        @if(auth()->user() && in_array(auth()->user()->role_id, [1, 2]))
        <button class="btn btn-primary" id="btn-create-project">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            New Project
        </button>
        @endif
    </div>
    <div class="dashboard-grid" id="projects-container">
        <div class="glass-panel" style="padding: 2rem; text-align: center; grid-column: 1 / -1;">
            <p>Loading projects...</p>
        </div>
    </div>
</div>
