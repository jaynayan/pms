<div id="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 999; backdrop-filter: blur(2px);"></div>

<div id="project-modal" class="glass-panel" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000; width: 90%; max-width: 500px; padding: 25px;">
    <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 1.25rem;" id="project-modal-title">Create Project</h3>
    <form id="create-project-form">
        <input type="hidden" name="project_id" id="edit-project-id">
        <div class="form-group" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: var(--text-muted);">Project Name</label>
            <input type="text" name="name" required style="width: 100%; padding: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: var(--text-main); font-family: inherit;">
        </div>
        <div class="form-group" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: var(--text-muted);">Description</label>
            <textarea name="description" rows="3" style="width: 100%; padding: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: var(--text-main); font-family: inherit; resize: vertical;"></textarea>
        </div>
        <div style="display: flex; gap: 15px; margin-bottom: 15px;">
            <div class="form-group" style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: var(--text-muted);">Start Date</label>
                <input type="date" name="start_date" style="width: 100%; padding: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: var(--text-main); font-family: inherit;">
            </div>
            <div class="form-group" style="flex: 1;">
                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: var(--text-muted);">End Date</label>
                <input type="date" name="end_date" style="width: 100%; padding: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: var(--text-main); font-family: inherit;">
            </div>
        </div>
        <div class="form-group" style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: var(--text-muted);">Status</label>
            <select name="status" style="width: 100%; padding: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: var(--text-main); font-family: inherit;">
                <option value="Planning" style="background: var(--bg-dark);">Planning</option>
                <option value="In Progress" style="background: var(--bg-dark);">In Progress</option>
                <option value="Review" style="background: var(--bg-dark);">Review</option>
                <option value="Completed" style="background: var(--bg-dark);">Completed</option>
            </select>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn btn-secondary" id="btn-close-modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="btn-save-project">Save Project</button>
        </div>
    </form>
</div>
