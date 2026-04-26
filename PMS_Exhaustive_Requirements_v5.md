# Software Requirements Specification (SRS)

## Project: Project Management System (PMS)
**Architecture:** Laravel API Backend + jQuery SPA Frontend  
**Document Version:** 5.0 (Corrected & Exhaustive Definition)

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
* **Auto-Provisioning:** On callback, the system checks if `provider_id` or `email` exists. If not, it creates a new user, syncing Name, Email, and Avatar URL. Users are assigned the default role of "Team Member".
* **SPA Session:** Login/Logout is handled via AJAX (Sanctum SPA Auth) to maintain the single-page experience.
* **Roles:** * **Admin:** Creates projects.
    * **Project Manager:** Task assignments.
    * **Team Member:** Execution/self-assignment.

#### 3.2 Project Management Module (Phase 1)
* **Core Container:** Admins define Projects (Name, Description, Start/End Dates, Status).
* **Team Mapping:** Admins assign users to specific projects (managed via the `project_user` pivot table).

#### 3.3 Jira-Style Backlog Module (Phase 1)
* **The Backlog:** A distinct view showing all tasks where `is_backlog = true`.
* **Prioritization:** Users can drag-and-drop tasks within the backlog to set priority (updates position via AJAX).
* **Self-Assignment ("Claiming"):** Team Members click "Claim" on a backlog item. This triggers a backend update setting `assigned_to = {user_id}`, `is_backlog = false`, and `status = 'To Do'`. The task instantly moves to the Kanban board.

#### 3.4 Visual Kanban Board Module (Phase 1)
* **Columns:** Three distinct states: "To Do", "In Progress", and "Done".
* **No-Reload Updates:** Moving a card between columns triggers a `PUT /tasks/{id}/status` request. The UI updates instantly on success.

#### 3.5 Communication & Tracking Modules (Phase 2 & 3)
* **Time Tracking (Phase 2):** Tasks hold an `original_estimate`. Users log actual hours worked, updating `total_spent`.
* **Collaboration (Phase 2):** Threaded comments attached to tasks. Task attachments for sharing files.
* **Activity Logging (Phase 2):** Automated system logs tracking when a task is claimed or changes status (records old and new values).
* **Notifications (Phase 3):** Internal app alerts and external email triggers for task assignments or mentions.

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
| role_id | `$table->foreignId('role_id')->default(3)->constrained();` | FK to roles. Default 3 (Team Member) |
| status | `$table->enum('status', ['active', 'inactive'])->default('active');` | Account status |

**Table: `roles`**

| Column Name | Laravel Migration Definition | Description |
| :--- | :--- | :--- |
| id | `$table->id();` | Primary Key |
| name | `$table->string('name');` | Admin, PM, Team Member |

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
| original_estimate | `$table->decimal('original_estimate', 8, 2)->nullable();` | Hours (Phase 2) |
| total_spent | `$table->decimal('total_spent', 8, 2)->default(0);` | Hours (Phase 2) |
| deleted_at | `$table->softDeletes();` | Soft delete support |

**Table: `task_comments` (Phase 2)**

| Column Name | Laravel Migration Definition | Description |
| :--- | :--- | :--- |
| id | `$table->id();` | Primary Key |
| task_id | `$table->foreignId('task_id')->constrained()->cascadeOnDelete();` | FK to tasks |
| user_id | `$table->foreignId('user_id')->constrained()->cascadeOnDelete();` | Author |
| comment | `$table->text('comment');` | Message body |

**Table: `task_activity_logs` (Phase 2)**

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

* **Phase 1:** Establish foundational database. Implement Laravel backend with Socialite for OAuth and Sanctum for SPA auth. Build the jQuery SPA interface. Connect the SPA to the API to handle Backlog population, Task Claiming, and Kanban drag-and-drop status updates.
* **Phase 2:** Expand the UI and API to support threaded comments on task modals, file attachments, implementation of actual time tracking inputs, and automated background logging of user actions on the board with old/new state comparisons.
* **Phase 3:** Finalize system loops by building the Notification panel in the SPA, integrating PHP Mail for assignment alerts, and generating administrative reports.
