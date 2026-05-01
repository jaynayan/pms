<div id="view-backlog" class="spa-view" style="display: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <h3 class="section-title" style="margin: 0;">Product Backlog</h3>
            <select class="project-selector" style="padding: 5px 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                <option value="" style="background-color: #1e1e2d; color: #ffffff;">Select a project...</option>
            </select>
        </div>
        <button class="btn btn-primary" id="btn-create-task-backlog" style="display:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            New Backlog Item
        </button>
    </div>
    
    <div id="backlog-empty-state" class="glass-panel" style="padding: 3rem; text-align: center;">
        <p>Please select a project from the Dashboard or Projects tab first.</p>
    </div>

    <div id="backlog-container" style="display: none;">
        <div class="glass-panel" style="padding: 1rem;">
            <div id="backlog-list" class="sortable-list" style="min-height: 100px; display: flex; flex-direction: column; gap: 10px;">
                <!-- Tasks will be injected here -->
            </div>
        </div>
    </div>
</div>
