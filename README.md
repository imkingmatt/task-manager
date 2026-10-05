# Task Management System

A role-based Task Management System built with Laravel 13. Regular users manage their own tasks; administrators manage tasks for every user.

## Tech Stack

- **Framework:** Laravel 13 (PHP 8.3+)
- **Auth:** Laravel Breeze (Blade)
- **Database:** MySQL
- **Local environment:** Laravel Sail (Docker)
- **Testing:** PHPUnit

## Features

- Email/password authentication (Laravel Breeze)
- Two roles: `user` and `admin`
- Regular users: create, view, edit, delete, and mark their own tasks complete
- Admins: full CRUD on every user's tasks via a separate admin panel
- Ownership enforced server-side via a Laravel Policy — a user can never edit/delete another user's task, even by guessing a URL
- Search by task title
- Paginated task lists (10 per page)

---

## Prerequisites

- **WSL2 + Ubuntu** (Windows) or any Linux/macOS shell
- **Docker Desktop**, with WSL2 integration enabled if on Windows
- No local PHP/Composer/MySQL install required — everything runs in Docker via Sail

Verify Docker is available:
```bash
docker --version
docker compose version
```

---

## Setup

### 1. Clone and enter the project

```bash
git clone <your-repo-url> task-manager
cd task-manager
```

### 2. Install PHP dependencies (via a temporary Docker container — no local PHP needed)

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

### 3. Create your environment file

```bash
cp .env.example .env
```

Confirm `.env` has (Sail's defaults — should already be set):
```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=sail
DB_PASSWORD=password
```

### 4. Start the containers

```bash
./vendor/bin/sail up -d
```

First run builds the images — give it a minute or two.

> **Tip:** add this alias to `~/.bashrc` so you can type `sail` instead of `./vendor/bin/sail` everywhere below:
> ```bash
> alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
> ```

### 5. Generate the app key, migrate, and seed

```bash
sail artisan key:generate
sail artisan migrate --seed
sail npm install
sail npm run build
```

Seeding creates a default admin account (see `database/seeders/DatabaseSeeder.php`):
```
Email:    admin@example.com
Password: password
```
Change this before deploying anywhere beyond local dev.

### 6. Open the app

Visit **http://localhost**. Register a normal account through the UI, or log in as the seeded admin above.

---

## Day-to-Day Sail Commands

| Task | Command |
|---|---|
| Start containers | `sail up -d` |
| Stop containers | `sail down` |
| Tail logs | `sail logs -f` |
| Run artisan | `sail artisan migrate` |
| Run composer | `sail composer require ...` |
| Run npm | `sail npm run dev` |
| Open MySQL shell | `sail mysql` |
| Open a shell in the app container | `sail shell` |
| Run tests | `sail artisan test` |
| Tinker | `sail artisan tinker` |

Every `php artisan`, `composer`, and `npm` command in this project runs through `sail` — never call the host's PHP/Composer/npm directly, since they're not required to be installed locally.

---

## Running Tests

```bash
sail artisan test
```

Tests live in `tests/Feature/TaskManagementTest.php` and cover:
- Task CRUD as a regular user
- Cross-user access returns `403` (ownership enforcement)
- Admin CRUD on any user's tasks
- Non-admin blocked from `/admin/*` routes
- Admin's own "My Tasks" view stays scoped to their own tasks

Tests run against a separate in-memory/test database state via `RefreshDatabase` — they won't touch your local dev data.

---

## Project Structure (task-related files)

```
app/
  Http/
    Controllers/
      TaskController.php          # regular-user task CRUD
      Admin/TaskController.php    # admin task CRUD (all users)
    Middleware/
      EnsureUserIsAdmin.php       # blocks non-admins from /admin/*
    Requests/
      StoreTaskRequest.php
      UpdateTaskRequest.php
  Models/
    Task.php
    User.php
  Policies/
    TaskPolicy.php                # ownership + admin-bypass authorization
  Services/
    TaskService.php                # business logic (listOwnedBy / listAll / CRUD)
database/
  migrations/
    ..._add_role_to_users_table.php
    ..._create_tasks_table.php
  seeders/
    DatabaseSeeder.php             # seeds the default admin
resources/views/
  layouts/navigation.blade.php     # nav links, admin link shown conditionally
  tasks/                           # regular-user views
  admin/tasks/                     # admin views
routes/
  web.php                          # user + admin route groups
tests/
  Feature/TaskManagementTest.php
```

---

## Roles & Access

| Route | Who can access | Enforced by |
|---|---|---|
| `/tasks` (+ create/edit/delete/toggle) | Any logged-in user, own tasks only | `auth` middleware + `TaskPolicy` |
| `/admin/tasks` (+ create/edit/delete) | Admins only, any user's tasks | `admin` middleware (`EnsureUserIsAdmin`) |

A user's role is stored in the `role` column on `users` (`user` or `admin`), set at registration (defaults to `user`) or directly in the database/seeder for admins.

---

## Troubleshooting

- **Port 80 already in use:** another service (or a previous Sail project) is bound to it. Stop it, or change `APP_PORT` in `.env` and re-run `sail up -d`.
- **`Target class [...] does not exist` errors:** usually a missing or mis-aliased `use` import in `routes/web.php` — see route file imports at the top.
- **`Call to undefined method ...::authorize()`:** the base `app/Http/Controllers/Controller.php` needs `use Illuminate\Foundation\Auth\Access\AuthorizesRequests;` and `use AuthorizesRequests;` inside the class (Laravel 11+ no longer includes this by default).
- **Styles/JS not updating:** run `sail npm run build` again, or `sail npm run dev` for a watch mode during active development.
- **Database connection refused from a host GUI tool:** connect to `127.0.0.1:3306` (not `mysql`) with the credentials from `.env` — `mysql` is only resolvable *inside* the Docker network.

---

## License

Internal/educational project — add a license here if distributing publicly.