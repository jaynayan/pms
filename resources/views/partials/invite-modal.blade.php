<div class="modal-overlay" id="invite-overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 1001; backdrop-filter: blur(4px);"></div>
<div class="modal glass-panel" id="invite-modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1002; width: 100%; max-width: 450px; padding: 2rem;">
    <h3 id="invite-modal-title" style="margin-top: 0; margin-bottom: 1.5rem; color: var(--text-main);">Invite User</h3>
    <form id="invite-user-form">
        <input type="hidden" id="invite-project-id" name="project_id">
        <div class="form-group" style="margin-bottom: 1.2rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; color: var(--text-muted);">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="colleague@example.com" required style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: var(--text-main);">
        </div>
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; color: var(--text-muted);">Role</label>
            <select name="role" class="form-control" required style="width: 100%; padding: 0.75rem; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: #1e1e2d; color: var(--text-main);">
                <option value="viewer">Viewer (Read-only)</option>
                <option value="member">Member (Can manage own tasks)</option>
                <option value="manager">Manager (Full project access)</option>
            </select>
        </div>
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" class="btn btn-secondary" id="btn-close-invite">Cancel</button>
            <button type="submit" class="btn btn-primary" id="btn-send-invite">Send Invite</button>
        </div>
    </form>
</div>
