<div id="view-board" class="spa-view" style="display: none;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <h3 class="section-title" style="margin: 0;">Kanban Board</h3>
            <select class="project-selector" style="padding: 5px 10px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: var(--text-main); font-family: inherit; font-size: 0.9rem;">
                <option value="" style="background-color: #1e1e2d; color: #ffffff;">Select a project...</option>
            </select>
        </div>
        <button class="btn btn-primary" id="btn-create-task-board" style="display:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            New Task
        </button>
    </div>

    <div id="board-empty-state" class="glass-panel" style="padding: 3rem; text-align: center;">
        <p>Please select a project from the Dashboard or Projects tab first.</p>
    </div>

    <div id="board-container" class="kanban-board" style="display: none; gap: 20px; overflow-x: auto; padding-bottom: 10px;">
        <!-- To Do Column -->
        <div class="kanban-column" style="flex: 1; min-width: 300px; display: flex; flex-direction: column;">
            <div class="kanban-column-header" style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <div style="width: 12px; height: 12px; border-radius: 50%; background: var(--primary);"></div>
                <h4 style="margin: 0;">To Do</h4>
                <span class="count-badge" id="count-todo" style="background: rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 12px; font-size: 0.8rem;">0</span>
            </div>
            <div class="glass-panel sortable-board" id="board-todo" data-status="To Do" style="flex: 1; min-height: 400px; padding: 10px; display: flex; flex-direction: column; gap: 10px;">
                <!-- Tasks -->
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="kanban-column" style="flex: 1; min-width: 300px; display: flex; flex-direction: column;">
            <div class="kanban-column-header" style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <div style="width: 12px; height: 12px; border-radius: 50%; background: var(--secondary);"></div>
                <h4 style="margin: 0;">In Progress</h4>
                <span class="count-badge" id="count-inprogress" style="background: rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 12px; font-size: 0.8rem;">0</span>
            </div>
            <div class="glass-panel sortable-board" id="board-inprogress" data-status="In Progress" style="flex: 1; min-height: 400px; padding: 10px; display: flex; flex-direction: column; gap: 10px;">
                <!-- Tasks -->
            </div>
        </div>

        <!-- Done Column -->
        <div class="kanban-column" style="flex: 1; min-width: 300px; display: flex; flex-direction: column;">
            <div class="kanban-column-header" style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                <div style="width: 12px; height: 12px; border-radius: 50%; background: #10b981;"></div>
                <h4 style="margin: 0;">Done</h4>
                <span class="count-badge" id="count-done" style="background: rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 12px; font-size: 0.8rem;">0</span>
            </div>
            <div class="glass-panel sortable-board" id="board-done" data-status="Done" style="flex: 1; min-height: 400px; padding: 10px; display: flex; flex-direction: column; gap: 10px;">
                <!-- Tasks -->
            </div>
        </div>
    </div>
</div>
