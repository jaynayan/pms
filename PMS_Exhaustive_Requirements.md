# Software Requirements Specification (SRS)

## Project: Project Management System (PMS)
**Architecture:** Laravel API Backend + jQuery SPA Frontend  
**Document Version:** 7.0 (Phase 1 Expansion - Comprehensive Task Management)

---

### 1. Overview
This document serves as the absolute master specification for the PMS. It integrates all Phase 1, 2, and 3 requirements, details the SPA module interactions, and defines a strict, 100% Laravel-compliant database schema specifically structured for agentic IDE ingestion.

### 2. System Architecture & Tech Stack
* **Backend:** Laravel (Latest). Acts as a stateless API for the frontend and handles OAuth via Laravel Socialite. Uses Laravel Sanctum for SPA cookie-based authentication.
* **Frontend:** Single Page Application (SPA) utilizing a single `index.html` container. Content and states are dynamically loaded via jQuery and AJAX.
* **Database:** MySQL, strictly adhering to Laravel Eloquent ORM conventions. Soft deletes implemented for critical records.
* **Authentication:** OAuth 2.0 (Google & Microsoft) exclusively. No local passwords.
* **Email Services:** PHP Inbuilt Mail Function / Laravel Mailables.

---

### 3. Exhaustive Module Breakdown

#### 3.1 Authentication & Profile Module (Phase 1)
* **Strict OAuth Entry:** No local registration forms. Users authenticate strictly via Google/Microsoft redirects.
* **Auto-Provisioning & Sandboxing:** On callback, the system checks if `provider_id` or `email` exists. If not, it creates a new user, syncing Name, Email, and Avatar URL. Users are assigned the default global role of "Viewer" (Role ID 4), effectively sandboxing them until they are invited to a project.
* **SPA Session:** Login/Logout is handled via AJAX (Sanctum SPA Auth) to maintain the single-page experience.
* **Hybrid Role System:** 
    * **Global Roles (`users.role_id`):** Dictates system-wide privileges (e.g., Admins and Project Managers can create new projects).
    * **Contextual Pivot Roles (`project_user.role`):** The primary security layer. Defines project-specific permissions as `manager` (full access), `member` (can manage tasks/claim tasks), or `viewer` (read-only).

#### 3.2 Project Management Module (Phase 1)
* **Core Container:** Admins define Projects (Name, Description, Start/End Dates, Status). The creator is automatically added to the project as a `manager`.
* **Team Mapping & Invites:** Managers use a dedicated API endpoint (`/api/projects/{id}/invite`) to invite users by email.
    * If the email exists, they are attached to the project via the `project_user` pivot table with their assigned role.
    * If the email does not exist, an OAuth "stub" account is generated so they can instantly link their account upon their first login.

#### 3.3 Jira-Style Backlog Module (Phase 1)
* **The Backlog:** A distinct view showing all tasks where `is_backlog = true`.
* **Prioritization:** Users can drag-and-drop tasks within the backlog to set priority (updates position via AJAX).
* **Self-Assignment ("Claiming"):** Team Members click "Claim" on a backlog item. This triggers a backend update setting `assigned_to = {user_id}`, `is_backlog = false`, and `status = 'To Do'`. The task instantly moves to the Kanban board.

#### 3.4 Visual Kanban Board Module (Phase 1)
* **Columns:** Three distinct states: "To Do", "In Progress", and "Done".
* **No-Reload Updates:** Moving a card between columns triggers a `PUT /tasks/{id}/status` request. The UI updates instantly on success.

#### 3.5 Communication & Tracking Modules (Phase 1 & Phase 2)
* **Detailed Task View (Phase 1):** Clicking a task opens a comprehensive modal with descriptions, assignment controls, and status dropdowns.
* **Time Estimation (Phase 1):** Tasks hold an `original_estimate` (hours).
* **Collaboration (Phase 1):** Threaded comments attached to tasks.
* **Activity Logging (Phase 1):** Automated system logs tracking when a task changes status, assignee, or estimate (records old and new values).
* **Email Notifications (Phase 1):** Automated email triggers sent to task assignees, creators, and watchers when crucial task updates occur.
* **Actual Time Tracking (Phase 2):** Users log actual hours worked, updating `total_spent`.
* **Task Attachments (Phase 2):** Uploading and sharing files within tasks.
* **App Alerts (Phase 3):** Internal app notification dropdown panel.

---

### 4. Comprehensive Database Schema (Laravel Eloquent Standard)
*All tables automatically include `$table->timestamps()` (`created_at` and `updated_at`).*

#### 4.1 Identity & Access

**Table: `users`**
*(Note: No password fields as auth is strictly OAuth)*

| Column Name | Laravel Migration Definition | Description |
| :--- | :--- | :--- |
| id | `$table->id();` | Primary Key |
| name | `$table->string('name');` | From OAuth |
| email | `$table->string('email')->unique();` | From OAuth |
| email_verified_at | `$table->timestamp('email_verified_at')->nullable();` | Standard Laravel |
| remember_token | `$table->rememberToken();` | Standard Laravel |
| provider_name | `$table->string('provider_name')->nullable();` | 'google' or 'microsoft' |
| provider_id | `$table->string('provider_id')->nullable();` | OAuth unique ID |
| avatar_url | `$table->string('avatar_url')->nullable();` | Profile image URL |
| role_id | `$table->foreignId('role_id')->default(4)->constrained();` | FK to roles. Default 4 (Viewer) |
| status | `$table->enum('status', ['active', 'inactive'])->default('active');` | Account status |

**Table: `roles` (Global System Roles)**

| Column Name | Laravel Migration Definition | Description |
| :--- | :--- | :--- |
| id | `$table->id();` | Primary Key |
| name | `$table->string('name');` | Admin, PM, Team Member, Viewer |

*(Note: `password_reset_tokens` table was removed as it is redundant in a strict OAuth system).*

#### 4.2 Workspace Container

**Table: `projects`**

| Column Name | Laravel Migration Definition | Description |
| :--- | :--- | :--- |
| id | `$table->id();` | Primary Key |
| name | `$table->string('name');` | Project Name |
| description | `$table->text('description')->nullable();` | Details |
| start_date | `$table->date('start_date')->nullable();` | Timeline Start |
| end_date | `$table->date('end_date')->nullable();` | Timeline End |
| status | `$table->string('status')->default('Active');` | Active/Archived |
| created_by | `$table->foreignId('created_by')->constrained('users');` | Creator ID |
| deleted_at | `$table->softDeletes();` | Soft delete support |

**Table: `project_user` (Pivot)**

| Column Name | Laravel Migration Definition | Description |
| :--- | :--- | :--- |
| project_id | `$table->foreignId('project_id')->constrained()->cascadeOnDelete();` | FK to projects |
| user_id | `$table->foreignId('user_id')->constrained()->cascadeOnDelete();` | FK to users |
| role | `$table->string('role')->default('member');` | Pivot role: manager, member, viewer |

#### 4.3 Agile Workflows & Tracking

**Table: `tasks`**

| Column Name | Laravel Migration Definition | Description |
| :--- | :--- | :--- |
| id | `$table->id();` | Primary Key |
| project_id | `$table->foreignId('project_id')->constrained()->cascadeOnDelete();` | Parent Project |
| creator_id | `$table->foreignId('creator_id')->constrained('users');` | Task Creator |
| assigned_to | `$table->foreignId('assigned_to')->nullable()->constrained('users');` | Null if in backlog |
| title | `$table->string('title');` | Summary |
| description | `$table->text('description')->nullable();` | Details |
| status | `$table->string('status')->default('To Do');` | To Do, In Progress, Done |
| priority | `$table->string('priority')->default('Medium');` | High, Medium, Low |
| due_date | `$table->date('due_date')->nullable();` | Deadline for the task |
| is_backlog | `$table->boolean('is_backlog')->default(true);` | Controls board visibility |
| position | `$table->integer('position')->default(0);` | Drag-and-drop order |
| original_estimate | `$table->decimal('original_estimate', 8, 2)->nullable();` | Hours (Phase 1) |
| total_spent | `$table->decimal('total_spent', 8, 2)->default(0);` | Hours (Phase 2) |
| deleted_at | `$table->softDeletes();` | Soft delete support |

**Table: `task_comments` (Phase 1)**

| Column Name | Laravel Migration Definition | Description |
| :--- | :--- | :--- |
| id | `$table->id();` | Primary Key |
| task_id | `$table->foreignId('task_id')->constrained()->cascadeOnDelete();` | FK to tasks |
| user_id | `$table->foreignId('user_id')->constrained()->cascadeOnDelete();` | Author |
| comment | `$table->text('comment');` | Message body |

**Table: `task_activity_logs` (Phase 1)**

| Column Name | Laravel Migration Definition | Description |
| :--- | :--- | :--- |
| id | `$table->id();` | Primary Key |
| task_id | `$table->foreignId('task_id')->constrained()->cascadeOnDelete();` | FK to tasks |
| user_id | `$table->foreignId('user_id')->constrained()->cascadeOnDelete();` | User who triggered action |
| action | `$table->string('action');` | e.g., 'Status Changed' |
| old_value | `$table->string('old_value')->nullable();` | e.g., 'To Do' |
| new_value | `$table->string('new_value')->nullable();` | e.g., 'In Progress' |

**Table: `task_attachments` (Phase 2)**

| Column Name | Laravel Migration Definition | Description |
| :--- | :--- | :--- |
| id | `$table->id();` | Primary Key |
| task_id | `$table->foreignId('task_id')->constrained()->cascadeOnDelete();` | FK to tasks |
| user_id | `$table->foreignId('user_id')->constrained()->cascadeOnDelete();` | Uploader |
| file_path | `$table->string('file_path');` | Path to stored file |
| file_name | `$table->string('file_name');` | Original file name |

#### 4.4 Notifications

**Table: `notifications` (Phase 3)**

| Column Name | Laravel Migration Definition | Description |
| :--- | :--- | :--- |
| id | `$table->id();` | Primary Key |
| user_id | `$table->foreignId('user_id')->constrained()->cascadeOnDelete();` | Recipient of alert |
| message | `$table->text('message');` | Alert text |
| is_read | `$table->boolean('is_read')->default(false);` | Read state boolean |

---

### 5. Execution Roadmap Summary

* **Phase 1:** Establish foundational database and complete "Core V1 Product". This includes OAuth backend, SPA routing, Kanban board, Project/Role management, detailed Task modals, Task Comments, Activity Logging Observers, Estimated Hours, and automated Email Notifications via Laravel Events.
* **Phase 2:** Expand task details with file attachments and actual time tracking (comparing `original_estimate` vs `total_spent`).
* **Phase 3:** Internal SPA notification panel and advanced administrative reporting.
