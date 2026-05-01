<div class="modal-overlay" id="task-details-overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 1001; backdrop-filter: blur(4px);"></div>
<div class="modal glass-panel" id="task-details-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1002; width: 100%; max-width: 600px; padding: 2rem; max-height: 90vh; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
        <h3 id="td-title" style="margin: 0; color: var(--text-main); font-size: 1.3rem;">Task Title</h3>
        <button type="button" id="btn-close-task-details" style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 5px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>
    
    <div style="margin-bottom: 1.5rem;">
        <h5 style="margin: 0 0 5px 0; color: var(--text-muted); font-size: 0.85rem;">Description</h5>
        <div id="td-description" style="color: var(--text-main); font-size: 0.95rem; line-height: 1.5; background: rgba(255,255,255,0.05); padding: 15px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.05); min-height: 60px;"></div>
    </div>
    
    <form id="task-details-form">
        <input type="hidden" id="td-task-id">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 1.5rem;">
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; color: var(--text-muted);">Status</label>
                <select id="td-status" name="status" class="form-control td-input" style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: #1e1e2d; color: var(--text-main);">
                    <option value="To Do">To Do</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Done">Done</option>
                </select>
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; color: var(--text-muted);">Estimated Hours</label>
                <input type="number" step="0.5" id="td-original-estimate" name="original_estimate" class="form-control td-input" placeholder="e.g. 4.5" style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: var(--text-main);">
            </div>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="submit" class="btn btn-primary td-action-btn" id="btn-save-task-details">Save Changes</button>
        </div>
    </form>

    <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">
    <h5 style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 10px;">Comments</h5>
    <div id="td-comments-list" style="margin-bottom: 15px; display: flex; flex-direction: column; gap: 10px; max-height: 200px; overflow-y: auto;">
        <!-- Comments will load here -->
    </div>
    
    <div id="td-comment-form-container">
        <form id="task-comment-form" style="display: flex; gap: 10px;">
            <input type="text" id="td-comment-input" required class="form-control" placeholder="Write a comment..." style="flex: 1; padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: var(--text-main);">
            <button type="submit" class="btn btn-secondary" id="btn-save-comment">Post</button>
        </form>
    </div>

    <hr style="border-color: rgba(255,255,255,0.1); margin: 20px 0;">
    <h5 style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 10px;">Activity History</h5>
    <div id="td-history-list" style="margin-bottom: 15px; display: flex; flex-direction: column; gap: 8px; max-height: 150px; overflow-y: auto;">
        <!-- History will load here -->
    </div>
</div>
