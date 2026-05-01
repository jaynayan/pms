const ProjectModule = (function () {
    function getStatusColor(status) {
        status = (status || '').toLowerCase();
        if (status.includes('progress')) return 'green';
        if (status.includes('plan')) return 'blue';
        if (status.includes('review')) return 'purple';
        if (status.includes('completed')) return 'gray';
        return 'purple';
    }

    function renderProjectCard(project) {
        const statusColor = getStatusColor(project.status);
        const dateRange = project.start_date && project.end_date ?
            `${project.start_date} to ${project.end_date}` :
            (project.start_date || 'No dates set');

        let avatarsHtml = '';
        if (project.users && project.users.length > 0) {
            avatarsHtml = '<div style="display:flex; margin-top: 15px;">';
            project.users.slice(0, 3).forEach((u, i) => {
                const img = u.avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=random&color=fff`;
                avatarsHtml += `<img src="${img}" title="${u.name}" style="width:28px; height:28px; border-radius:50%; border:2px solid var(--bg-card); margin-left: ${i === 0 ? 0 : '-10px'}; z-index:${10 - i}">`;
            });
            if (project.users.length > 3) {
                avatarsHtml += `<div style="width:28px; height:28px; border-radius:50%; border:2px solid var(--bg-card); margin-left:-10px; background:var(--bg-dark); display:flex; align-items:center; justify-content:center; font-size:10px; z-index:1;">+${project.users.length - 3}</div>`;
            }
            avatarsHtml += '</div>';
        }

        const myRole = project.users?.find(u => u.id === window.currentUser?.id)?.pivot?.role;
        const isManager = myRole === 'manager' || project.created_by === window.currentUser?.id;

        let actionBtns = '';
        if (isManager) {
            actionBtns = `
                <button class="btn btn-secondary btn-invite-project" data-id="${project.id}" style="padding: 4px 8px; font-size: 0.75rem; background: var(--primary); border: none; color: white;">Invite</button>
                <button class="btn btn-secondary btn-edit-project" data-id="${project.id}" style="padding: 4px 8px; font-size: 0.75rem;">Edit</button>
                <button class="btn btn-secondary btn-delete-project" data-id="${project.id}" style="padding: 4px 8px; font-size: 0.75rem;">Delete</button>
            `;
        }

        return `
            <div class="project-card glass-panel" data-id="${project.id}">
                <div class="project-header">
                    <h4 style="margin: 0; font-size: 1.1rem; color: var(--text-main);">${project.name}</h4>
                    <span class="status-pill ${statusColor}">${project.status || 'Active'}</span>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 15px; height: 40px; overflow: hidden; text-overflow: ellipsis;">
                    ${project.description || 'No description provided.'}
                </p>
                <div class="progress-container">
                    <div class="progress-info" style="margin-bottom: 5px;">
                        <span style="font-size: 0.8rem; color: var(--text-muted);">${dateRange}</span>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                    ${avatarsHtml}
                    <div style="display: flex; gap: 5px;">
                        ${actionBtns}
                    </div>
                </div>
            </div>
        `;
    }

    function loadProjects() {
        $('#projects-container').html('<div class="glass-panel" style="padding: 2rem; text-align: center; grid-column: 1 / -1;"><p>Loading projects...</p></div>');
        $.get('/api/projects', function (projects) {
            if (projects.length === 0) {
                $('#projects-container').html(`
                    <div class="glass-panel" style="padding: 3rem; text-align: center; grid-column: 1 / -1; display:flex; flex-direction:column; align-items:center;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted); margin-bottom:15px;"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                        <h4 style="margin-bottom: 10px;">No Projects Found</h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Create your first project to get started.</p>
                    </div>
                `);
                return;
            }

            let html = '';
            projects.forEach(p => { html += renderProjectCard(p); });
            $('#projects-container').html(html);
        });
    }

    function loadDashboardProjects() {
        $.get('/api/projects', function (projects) {
            if (projects.length === 0) {
                $('#dashboard-recent-projects').html('<p class="text-muted">No projects available.</p>');
                return;
            }
            let html = '';
            projects.slice(0, 3).forEach(p => { html += renderProjectCard(p); });
            $('#dashboard-recent-projects').html(html);
        });
    }

    function init() {
        // Modal Interactions
        $('#btn-create-project').click(() => {
            $('#project-modal-title').text('Create Project');
            $('#edit-project-id').val('');
            $('#create-project-form')[0].reset();
            $('#project-modal, #modal-overlay').fadeIn(200);
        });

        $('#btn-close-modal, #modal-overlay').click((e) => {
            if (e.target.id === 'modal-overlay' && $('#task-modal').is(':visible')) return;
            $('#project-modal, #modal-overlay').fadeOut(200);
            $('#create-project-form')[0].reset();
            $('#edit-project-id').val('');
        });

        // Edit Project
        $(document).on('click', '.btn-edit-project', function (e) {
            e.stopPropagation();
            const id = $(this).data('id');
            const btn = $(this);
            btn.text('...').prop('disabled', true);

            $.get(`/api/projects/${id}`, function (project) {
                btn.text('Edit').prop('disabled', false);
                $('#project-modal-title').text('Edit Project');
                $('#edit-project-id').val(project.id);
                $('#create-project-form [name="name"]').val(project.name);
                $('#create-project-form [name="description"]').val(project.description);
                $('#create-project-form [name="start_date"]').val(project.start_date || '');
                $('#create-project-form [name="end_date"]').val(project.end_date || '');
                $('#create-project-form [name="status"]').val(project.status || 'Planning');
                $('#project-modal, #modal-overlay').fadeIn(200);
            }).fail(function () {
                btn.text('Edit').prop('disabled', false);
                alert('Error loading project details.');
            });
        });

        // Create Project Form Submit
        $('#create-project-form').submit(function (e) {
            e.preventDefault();

            const btn = $('#btn-save-project');
            const originalText = btn.text();
            btn.text('Saving...').prop('disabled', true);

            const data = $(this).serialize();
            const editId = $('#edit-project-id').val();
            const url = editId ? `/api/projects/${editId}` : '/api/projects';
            const method = editId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: data,
                success: function (response) {
                    $('#project-modal, #modal-overlay').fadeOut(200);
                    $('#create-project-form')[0].reset();
                    $('#edit-project-id').val('');
                    if ($('#view-projects').is(':visible')) {
                        loadProjects();
                    } else if ($('#view-dashboard').is(':visible')) {
                        loadDashboardProjects();
                    }
                    if (typeof TaskModule !== 'undefined') {
                        TaskModule.populateProjectSelectors();
                    }
                    showToast('Project ' + (editId ? 'updated' : 'created') + ' successfully', 'success');
                },
                error: function (xhr) {
                    showToast('Error saving project.', 'error');
                    console.error(xhr.responseText);
                },
                complete: function () {
                    btn.text(originalText).prop('disabled', false);
                }
            });
        });

        // Delete Project (Event Delegation)
        $(document).on('click', '.btn-delete-project', function (e) {
            e.stopPropagation(); // prevent card click
            if (confirm('Are you sure you want to delete this project?')) {
                const id = $(this).data('id');
                const card = $(this).closest('.project-card');
                card.css('opacity', '0.5');

                $.ajax({
                    url: `/api/projects/${id}`,
                    method: 'DELETE',
                    success: function () {
                        card.fadeOut(300, function () { $(this).remove(); });
                        if (typeof TaskModule !== 'undefined') {
                            TaskModule.populateProjectSelectors();
                        }
                        showToast('Project deleted successfully', 'success');
                    },
                    error: function (xhr) {
                        card.css('opacity', '1');
                        showToast('Error deleting project', 'error');
                    }
                });
            }
        });

        // Select Project
        $(document).on('click', '.project-card', function (e) {
            if ($(e.target).closest('button').length) return; // Ignore clicks on buttons
            const id = $(this).data('id');
            const name = $(this).find('h4').text();
            window.currentProjectId = id;
            window.currentProjectName = name;

            // Fetch full project details to store the current user's role for Tasks view
            $.get(`/api/projects/${id}`, function (project) {
                const myRole = project.users?.find(u => u.id === window.currentUser?.id)?.pivot?.role;
                window.currentUserRole = myRole || (project.created_by === window.currentUser?.id ? 'manager' : 'viewer');

                // Navigate to board
                window.location.hash = '#board';
            });
        });

        // Invite Modal
        $(document).on('click', '.btn-invite-project', function (e) {
            e.stopPropagation();
            const id = $(this).data('id');
            $('#invite-project-id').val(id);
            $('#invite-user-form')[0].reset();
            $('#invite-modal, #invite-overlay').fadeIn(200);
        });

        $('#btn-close-invite, #invite-overlay').click((e) => {
            if (e.target.id === 'invite-overlay' && false) return; // overlay close
            $('#invite-modal, #invite-overlay').fadeOut(200);
        });

        $('#invite-user-form').submit(function (e) {
            e.preventDefault();
            const btn = $('#btn-send-invite');
            btn.prop('disabled', true).text('Sending...');

            const id = $('#invite-project-id').val();

            $.ajax({
                url: `/api/projects/${id}/invite`,
                method: 'POST',
                data: $(this).serialize(),
                success: function () {
                    $('#invite-modal, #invite-overlay').fadeOut(200);
                    showToast('User invited successfully', 'success');
                    if ($('#view-projects').is(':visible')) loadProjects();
                    if ($('#view-dashboard').is(':visible')) loadDashboardProjects();
                },
                error: function (xhr) {
                    showToast('Error inviting user: ' + (xhr.responseJSON?.message || 'Check email and role'), 'error');
                },
                complete: function () {
                    btn.prop('disabled', false).text('Send Invite');
                }
            });
        });
    }

    return {
        init,
        loadProjects,
        loadDashboardProjects
    };
})();
