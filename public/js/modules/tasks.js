const TaskModule = (function () {

    function renderTaskCard(task) {
        const priorityColor = task.priority === 'High' ? '#ef4444' : (task.priority === 'Medium' ? '#f59e0b' : '#3b82f6');

        let assigneeHtml = '';
        if (task.assignee) {
            const img = task.assignee.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(task.assignee.name)}&background=random&color=fff`;
            assigneeHtml = `<img src="${img}" title="${task.assignee.name}" style="width:24px; height:24px; border-radius:50%; border:2px solid var(--bg-card);">`;
        } else if (task.is_backlog && window.currentUserRole !== 'viewer') {
            assigneeHtml = `<button class="btn btn-secondary btn-claim-task" data-id="${task.id}" style="padding: 2px 8px; font-size: 0.7rem; background: var(--primary); border: none; color: white;">Claim</button>`;
        }

        return `
            <div class="task-card glass-panel" data-id="${task.id}" style="padding: 15px; cursor: grab; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <span style="font-size: 0.7rem; font-weight: 600; color: ${priorityColor}; text-transform: uppercase; letter-spacing: 0.5px;">${task.priority || 'Medium'}</span>
                    ${assigneeHtml}
                </div>
                <h5 style="margin: 0 0 5px 0; font-size: 0.95rem; color: var(--text-main);">${task.title}</h5>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${task.description || ''}</p>
            </div>
        `;
    }

    function populateProjectSelectors() {
        $.get('/api/projects', function (projects) {
            let options = '<option value="" style="background-color: #1e1e2d; color: #ffffff;">Select a project...</option>';
            projects.forEach(p => {
                options += `<option value="${p.id}" style="background-color: #1e1e2d; color: #ffffff;">${p.name}</option>`;
            });
            $('.project-selector').html(options);
            if (window.currentProjectId) {
                $('.project-selector').val(window.currentProjectId);
            }
        });
    }

    function ensureProjectSelectors() {
        if ($('.project-selector').first().find('option').length <= 1) {
            populateProjectSelectors();
        } else if (window.currentProjectId) {
            $('.project-selector').val(window.currentProjectId);
        }
    }

    function loadBacklog() {
        ensureProjectSelectors();
        if (!window.currentProjectId) {
            $('#backlog-empty-state').show();
            $('#backlog-container, #btn-create-task-backlog').hide();
            return;
        }

        $('#backlog-empty-state').hide();
        $('#backlog-container').show();

        if (window.currentUserRole === 'viewer') {
            $('#btn-create-task-backlog').hide();
        } else {
            $('#btn-create-task-backlog').show();
        }

        $('#backlog-list').html('<p class="text-muted" style="text-align: center; padding: 20px;">Loading backlog...</p>');

        $.get(`/api/tasks/backlog?project_id=${window.currentProjectId}`, function (tasks) {
            if (tasks.length === 0) {
                $('#backlog-list').html('<p class="text-muted" style="text-align: center; padding: 20px;">Backlog is empty. Create a task to get started.</p>');
                return;
            }

            let html = '';
            tasks.forEach(t => html += renderTaskCard(t));
            $('#backlog-list').html(html);
        });
    }

    function loadBoard() {
        ensureProjectSelectors();
        if (!window.currentProjectId) {
            $('#board-empty-state').show();
            $('#board-container, #btn-create-task-board').hide();
            return;
        }

        $('#board-empty-state').hide();
        $('#board-container').css('display', 'flex');

        if ($('#btn-create-task-board').length) {
            if (window.currentUserRole === 'viewer') {
                $('#btn-create-task-board').hide();
            } else {
                $('#btn-create-task-board').show();
            }
        }

        $('#board-todo, #board-inprogress, #board-done').html('<p class="text-muted" style="font-size: 0.8rem; text-align: center;">Loading...</p>');

        $.get(`/api/tasks?project_id=${window.currentProjectId}`, function (tasks) {
            $('#board-todo, #board-inprogress, #board-done').empty();

            let counts = { 'To Do': 0, 'In Progress': 0, 'Done': 0 };

            tasks.forEach(t => {
                let status = t.status || 'To Do';
                if (!counts[status] && counts[status] !== 0) status = 'To Do';

                let targetId = '#board-todo';
                if (status === 'In Progress') targetId = '#board-inprogress';
                if (status === 'Done') targetId = '#board-done';

                $(targetId).append(renderTaskCard(t));
                counts[status]++;
            });

            $('#count-todo').text(counts['To Do']);
            $('#count-inprogress').text(counts['In Progress']);
            $('#count-done').text(counts['Done']);
        });
    }

    function initSortable() {
        if (typeof Sortable === 'undefined') {
            setTimeout(initSortable, 100);
            return;
        }

        if ($('#backlog-list').length) {
            new Sortable(document.getElementById('backlog-list'), {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function (evt) {
                    const tasks = [];
                    $('#backlog-list .task-card').each(function (index) {
                        tasks.push({
                            id: $(this).data('id'),
                            position: index + 1
                        });
                    });

                    $.ajax({
                        url: '/api/tasks/reorder',
                        method: 'POST',
                        data: { tasks: tasks }
                    });
                }
            });
        }

        ['board-todo', 'board-inprogress', 'board-done'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                new Sortable(el, {
                    group: 'kanban',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: function (evt) {
                        const newStatus = $(evt.to).data('status');

                        const tasks = [];
                        $(evt.to).find('.task-card').each(function (index) {
                            tasks.push({
                                id: $(this).data('id'),
                                position: index + 1,
                                status: newStatus
                            });
                        });

                        $.ajax({
                            url: '/api/tasks/reorder',
                            method: 'POST',
                            data: { tasks: tasks },
                            success: function () {
                                $('#count-todo').text($('#board-todo .task-card').length);
                                $('#count-inprogress').text($('#board-inprogress .task-card').length);
                                $('#count-done').text($('#board-done .task-card').length);
                            }
                        });
                    }
                });
            }
        });
    }

    function init() {
        $(document).on('change', '.project-selector', function () {
            const id = $(this).val();
            const name = $(this).find('option:selected').text();
            if (id) {
                window.currentProjectId = id;
                window.currentProjectName = name;
                $('.project-selector').val(id); // sync across both views
                // Fetch role before loading
                $.get(`/api/projects/${id}`, function (project) {
                    window.currentProjectDetails = project;
                    const myRole = project.users?.find(u => u.id === window.currentUser?.id)?.pivot?.role;
                    window.currentUserRole = myRole || (project.created_by === window.currentUser?.id ? 'manager' : 'viewer');

                    if ($('#view-backlog').is(':visible')) loadBacklog();
                    if ($('#view-board').is(':visible')) loadBoard();
                });
            } else {
                window.currentProjectId = null;
                window.currentProjectName = null;
                $('.project-selector').val('');
                if ($('#view-backlog').is(':visible')) loadBacklog();
                if ($('#view-board').is(':visible')) loadBoard();
            }
        });

        function populateAssigneeDropdown() {
            const dropdown = $('#task-assignee');
            dropdown.html('<option value="" style="background: var(--bg-dark);">Unassigned</option>');

            if (!window.currentProjectDetails || !window.currentProjectDetails.users) return;

            if (window.currentUserRole === 'member') {
                dropdown.append(`<option value="${window.currentUser.id}" style="background: var(--bg-dark);">Assign to Me (${window.currentUser.name})</option>`);
            } else if (window.currentUserRole === 'manager') {
                window.currentProjectDetails.users.forEach(u => {
                    dropdown.append(`<option value="${u.id}" style="background: var(--bg-dark);">${u.name} (${u.email})</option>`);
                });
            }
        }

        $('#btn-create-task-backlog').click(() => {
            $('#task-is-backlog').val(1);
            $('#task-project-id').val(window.currentProjectId);
            populateAssigneeDropdown();
            $('#task-modal, #modal-overlay').fadeIn(200);
        });

        $('#btn-create-task-board').click(() => {
            $('#task-is-backlog').val(0);
            $('#task-project-id').val(window.currentProjectId);
            populateAssigneeDropdown();
            $('#task-modal, #modal-overlay').fadeIn(200);
        });

        $('#btn-close-task-modal, #modal-overlay').click((e) => {
            if (e.target.id === 'modal-overlay' && $('#project-modal').is(':visible')) return;
            $('#task-modal, #modal-overlay').fadeOut(200);
            $('#create-task-form')[0].reset();
        });

        $('#create-task-form').submit(function (e) {
            e.preventDefault();
            const btn = $('#btn-save-task');
            btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: '/api/tasks',
                method: 'POST',
                data: $(this).serialize(),
                success: function () {
                    $('#task-modal, #modal-overlay').fadeOut(200);
                    $('#create-task-form')[0].reset();
                    if ($('#view-backlog').is(':visible')) loadBacklog();
                    if ($('#view-board').is(':visible')) loadBoard();
                    showToast('Task saved successfully', 'success');
                },
                error: function () {
                    showToast('Error saving task', 'error');
                },
                complete: function () {
                    btn.prop('disabled', false).text('Save Task');
                }
            });
        });

        $(document).on('click', '.btn-claim-task', function (e) {
            e.stopPropagation();
            const btn = $(this);
            const taskId = btn.data('id');

            btn.prop('disabled', true).text('...');

            $.ajax({
                url: `/api/tasks/${taskId}/claim`,
                method: 'POST',
                success: function () {
                    loadBacklog();
                    showToast('Task claimed successfully', 'success');
                },
                error: function () {
                    btn.prop('disabled', false).text('Claim');
                    showToast('Error claiming task', 'error');
                }
            });
        });

        // Task Details Modal
        $(document).on('click', '.task-card', function (e) {
            if ($(e.target).closest('button').length) return;

            const taskId = $(this).data('id');
            $('#td-task-id').val(taskId);

            $('#td-title').text('Loading...');
            $('#td-description').html('<em style="color:var(--text-muted);">Loading...</em>');
            $('#td-status').val('');
            $('#td-original-estimate').val('');

            $('#task-details-modal, #task-details-overlay').fadeIn(200);

            $.get(`/api/tasks/${taskId}`, function (task) {
                $('#td-title').text(task.title);
                $('#td-description').html(task.description ? task.description.replace(/\n/g, '<br>') : '<em style="color:var(--text-muted);">No description provided.</em>');
                $('#td-status').val(task.status || 'To Do');
                $('#td-original-estimate').val(task.original_estimate || '');

                if (window.currentUserRole === 'viewer') {
                    $('.td-input').prop('disabled', true).css('opacity', '0.6');
                    $('.td-action-btn').hide();
                    $('#td-comment-form-container').hide();
                } else {
                    $('.td-input').prop('disabled', false).css('opacity', '1');
                    $('.td-action-btn').show();
                    $('#td-comment-form-container').show();
                }
                loadComments(taskId);
                loadHistory(taskId);
            }).fail(function () {
                showToast('Failed to load task details', 'error');
                $('#task-details-modal, #task-details-overlay').fadeOut(200);
            });
        });

        $('#btn-close-task-details, #task-details-overlay').click((e) => {
            if (e.target.id === 'task-details-overlay' || $(e.target).closest('#btn-close-task-details').length) {
                $('#task-details-modal, #task-details-overlay').fadeOut(200);
            }
        });

        $('#task-details-form').submit(function (e) {
            e.preventDefault();
            const btn = $('#btn-save-task-details');
            btn.prop('disabled', true).text('Saving...');

            const taskId = $('#td-task-id').val();
            const status = $('#td-status').val();
            const original_estimate = $('#td-original-estimate').val();

            const statusReq = $.ajax({
                url: `/api/tasks/${taskId}/status`,
                method: 'PATCH',
                data: { status: status }
            });

            const updateReq = $.ajax({
                url: `/api/tasks/${taskId}`,
                method: 'PUT',
                data: { original_estimate: original_estimate }
            });

            $.when(statusReq, updateReq).done(function () {
                $('#task-details-modal, #task-details-overlay').fadeOut(200);
                showToast('Task updated successfully', 'success');
                if ($('#view-backlog').is(':visible')) loadBacklog();
                if ($('#view-board').is(':visible')) loadBoard();

                // Re-fetch to ensure the observer logs the new status immediately if they reopen
            }).fail(function () {
                showToast('Error updating task. Some changes may not have saved.', 'error');
            }).always(function () {
                btn.prop('disabled', false).text('Save Changes');
            });
        });

        function loadComments(taskId) {
            $('#td-comments-list').html('<p style="font-size:0.8rem; color:var(--text-muted);">Loading comments...</p>');
            $.get(`/api/tasks/${taskId}/comments`, function (comments) {
                if (comments.length === 0) {
                    $('#td-comments-list').html('<p style="font-size:0.8rem; color:var(--text-muted);">No comments yet.</p>');
                    return;
                }

                let html = '';
                comments.forEach(c => {
                    const isAuthor = c.user_id === window.currentUser.id;
                    const canDelete = isAuthor || window.currentUserRole === 'manager';

                    let deleteBtn = '';
                    if (canDelete) {
                        deleteBtn = `<button class="btn-delete-comment" data-id="${c.id}" style="background:none; border:none; color:#ef4444; font-size:0.75rem; cursor:pointer;">Delete</button>`;
                    }

                    const date = new Date(c.created_at).toLocaleString();
                    html += `
                        <div style="background: rgba(255,255,255,0.03); padding: 10px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.05);">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <strong style="font-size: 0.85rem; color: var(--text-main);">${c.user.name}</strong>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">${date} ${deleteBtn}</span>
                            </div>
                            <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">${c.comment}</p>
                        </div>
                    `;
                });
                $('#td-comments-list').html(html);
            });
        }

        function loadHistory(taskId) {
            $('#td-history-list').html('<p style="font-size:0.8rem; color:var(--text-muted);">Loading history...</p>');
            $.get(`/api/tasks/${taskId}/history`, function (logs) {
                if (logs.length === 0) {
                    $('#td-history-list').html('<p style="font-size:0.8rem; color:var(--text-muted);">No activity recorded yet.</p>');
                    return;
                }

                let html = '';
                logs.forEach(log => {
                    const date = new Date(log.created_at).toLocaleString();
                    const user = log.user ? log.user.name : 'System';

                    let details = '';
                    if (log.old_value || log.new_value) {
                        details = `<span style="color: #a78bfa;">${log.old_value || 'None'}</span> &rarr; <span style="color: #34d399;">${log.new_value || 'None'}</span>`;
                    }

                    html += `
                        <div style="font-size: 0.8rem; color: var(--text-muted); padding: 5px 0; border-bottom: 1px dashed rgba(255,255,255,0.05);">
                            <strong>${user}</strong> ${log.action.toLowerCase()} ${details}
                            <span style="float:right; font-size: 0.7rem; opacity: 0.6;">${date}</span>
                        </div>
                    `;
                });
                $('#td-history-list').html(html);
            });
        }

        $('#task-comment-form').submit(function (e) {
            e.preventDefault();
            const taskId = $('#td-task-id').val();
            const comment = $('#td-comment-input').val();
            const btn = $('#btn-save-comment');
            btn.prop('disabled', true).text('...');

            $.ajax({
                url: `/api/tasks/${taskId}/comments`,
                method: 'POST',
                data: { comment: comment },
                success: function () {
                    $('#td-comment-input').val('');
                    loadComments(taskId);
                },
                error: function () {
                    showToast('Failed to post comment', 'error');
                },
                complete: function () {
                    btn.prop('disabled', false).text('Post');
                }
            });
        });

        $(document).on('click', '.btn-delete-comment', function () {
            if (!confirm('Delete this comment?')) return;
            const commentId = $(this).data('id');
            const taskId = $('#td-task-id').val();

            $.ajax({
                url: `/api/tasks/comments/${commentId}`,
                method: 'DELETE',
                success: function () {
                    loadComments(taskId);
                },
                error: function () {
                    showToast('Failed to delete comment', 'error');
                }
            });
        });

        initSortable();
    }

    return {
        init,
        loadBacklog,
        loadBoard,
        populateProjectSelectors
    };
})();
