<div id="task-modal" class="glass-panel" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1000; width: 90%; max-width: 500px; padding: 25px;">
    <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 1.25rem;">Create Task</h3>
    <form id="create-task-form">
        <input type="hidden" name="project_id" id="task-project-id">
        <input type="hidden" name="is_backlog" id="task-is-backlog" value="1">
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: var(--text-muted);">Task Title</label>
            <input type="text" name="title" required style="width: 100%; padding: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: var(--text-main); font-family: inherit;">
        </div>
        <div class="form-group" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: var(--text-muted);">Description</label>
            <textarea name="description" rows="3" style="width: 100%; padding: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: var(--text-main); font-family: inherit; resize: vertical;"></textarea>
        </div>
        <div class="form-group" style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: var(--text-muted);">Priority</label>
            <select name="priority" style="width: 100%; padding: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: var(--text-main); font-family: inherit;">
                <option value="Low" style="background: var(--bg-dark);">Low</option>
                <option value="Medium" style="background: var(--bg-dark);" selected>Medium</option>
                <option value="High" style="background: var(--bg-dark);">High</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: var(--text-muted);">Assignee</label>
            <select name="assigned_to" id="task-assignee" style="width: 100%; padding: 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: var(--text-main); font-family: inherit;">
                <option value="" style="background: var(--bg-dark);">Unassigned</option>
            </select>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn btn-secondary" id="btn-close-task-modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="btn-save-task">Save Task</button>
        </div>
    </form>
</div>
