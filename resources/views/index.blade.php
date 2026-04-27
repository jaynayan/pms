<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PMS - Project Management System</title>
    <meta name="description" content="A powerful, modern Project Management System">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
</head>
<body>
    <div class="app-container">
        @auth
        <!-- Sidebar Navigation -->
        <aside class="sidebar glass-panel">
            <div class="logo-container">
                <div class="logo-icon"></div>
                <h1>PMS<span>.</span></h1>
            </div>
            
            <nav class="main-nav">
                <a href="#dashboard" class="nav-item active" data-target="dashboard">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard
                </a>
                <a href="#projects" class="nav-item" data-target="projects">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                    Projects
                </a>
                <a href="#backlog" class="nav-item" data-target="backlog">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                    Backlog
                </a>
                <a href="#board" class="nav-item" data-target="board">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                    Kanban Board
                </a>
            </nav>
            
            <div class="user-auth-section">
                <div class="auth-buttons" id="auth-container">
                    <div style="display: flex; align-items: center; gap: 10px; color: var(--text-main); margin-bottom: 15px;">
                        <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=7c3aed&color=fff' }}" alt="Profile" style="width: 32px; height: 32px; border-radius: 50%;">
                        <div style="display: flex; flex-direction: column; flex: 1; overflow: hidden;">
                            <span style="font-size: 0.9rem; font-weight: 500; white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">{{ auth()->user()->name }}</span>
                            <span style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; text-overflow: ellipsis; overflow: hidden;">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                    <form method="POST" action="/logout" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-secondary login-btn" style="padding: 0.5rem; font-size: 0.85rem;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        @endauth

        <!-- Main Content Area -->
        <main class="main-content">
            @auth
            <header class="topbar glass-panel">
                <div class="page-title">
                    <h2 id="current-page-title">Dashboard</h2>
                </div>
                <div class="topbar-actions">
                    <div class="search-bar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <input type="text" placeholder="Search tasks, projects...">
                    </div>
                    <button class="icon-btn notification-btn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                        <span class="notification-badge"></span>
                    </button>
                </div>
            </header>
            @endauth

            <div class="content-wrapper" id="app-content">
                @guest
                <!-- Guest Landing Page -->
                <div class="hero-section glass-panel" style="margin: auto; max-width: 800px; margin-top: 10vh; justify-content: center; text-align: center;">
                    <div class="hero-content" style="max-width: 600px; display: flex; flex-direction: column; align-items: center;">
                        <div class="logo-container" style="margin-bottom: 2rem; display: flex; align-items: center; justify-content: center; gap: 12px;">
                            <div class="logo-icon"></div>
                            <h1 style="font-size: 1.5rem; font-weight: 700; letter-spacing: -0.5px;">PMS<span style="color: var(--primary);">.</span></h1>
                        </div>
                        <h1 class="gradient-text">Manage Your Projects With Elegance</h1>
                        <p>Experience seamless collaboration, powerful tracking, and intuitive workflows in one beautiful workspace.</p>
                        <div class="hero-actions" style="margin-top: 2rem; justify-content: center;">
                            <button class="btn btn-primary" onclick="window.location.href='/auth/google/redirect'" style="padding: 0.8rem 1.5rem; font-size: 1.05rem;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                                Login with Google
                            </button>
                        </div>
                    </div>
                </div>
                @endguest

                @auth
                <!-- Dashboard content for authenticated users -->
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
                        <div class="project-list">
                            <div class="project-card glass-panel">
                                <div class="project-header">
                                    <h4>Alpha Phase Redesign</h4>
                                    <span class="status-pill green">In Progress</span>
                                </div>
                                <div class="progress-container">
                                    <div class="progress-info">
                                        <span>Completion</span>
                                        <span>65%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 65%;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="project-card glass-panel">
                                <div class="project-header">
                                    <h4>Backend API Migration</h4>
                                    <span class="status-pill blue">Planning</span>
                                </div>
                                <div class="progress-container">
                                    <div class="progress-info">
                                        <span>Completion</span>
                                        <span>15%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 15%;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="project-card glass-panel">
                                <div class="project-header">
                                    <h4>Q3 Marketing Site</h4>
                                    <span class="status-pill purple">Review</span>
                                </div>
                                <div class="progress-container">
                                    <div class="progress-info">
                                        <span>Completion</span>
                                        <span>90%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: 90%;"></div>
                                    </div>
                                </div>
                            </div>
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
                            <label class="task-item">
                                <input type="checkbox" class="custom-checkbox">
                                <span class="task-title">Update dependencies</span>
                                <span class="task-date">Tomorrow</span>
                            </label>
                            <label class="task-item">
                                <input type="checkbox" class="custom-checkbox">
                                <span class="task-title">Schedule sprint planning</span>
                                <span class="task-date">Apr 30</span>
                            </label>
                        </div>
                    </div>
                </div>
                @endauth
            </div>
        </main>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- App Logic -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
