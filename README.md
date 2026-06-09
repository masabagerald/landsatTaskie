# LandsatTaskie

A team task management web application built with Laravel 13, featuring a dashboard with KPI metrics, task/category/user CRUD operations, and Laravel Breeze authentication.

## Features

- **Dashboard** — KPI cards for total, completed, pending, and overdue tasks; personal task list with quick status actions
- **Task Management** — Create, update, and delete tasks with priority levels, statuses, due dates, category assignment, and user assignment
- **Category Management** — Organize tasks into named categories
- **User Management** — Manage team members and assign them to tasks
- **Authentication** — Register, login, password reset, and email verification via Laravel Breeze

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.3+, Laravel 13.8 |
| Frontend | Blade, Bootstrap 5, Tailwind CSS, Alpine.js |
| Build | Vite 8 |
| Database | SQLite (default) / MySQL |
| Testing | PHPUnit 12 |

## Requirements

- PHP >= 8.3
- Composer
- Node.js >= 18 & npm

## Installation

```bash
git clone <repository-url>
cd landsatTaskie

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed   # optional — seeds a test user
```

## Running Locally

```bash
composer run dev
```

This starts all services concurrently:

| Service | Command |
|---------|---------|
| Web server | `php artisan serve` (http://localhost:8000) |
| Queue worker | `php artisan queue:listen` |
| Log stream | `php artisan pail` |
| Vite dev server | `npm run dev` |

## Database Schema

| Table | Key Columns |
|-------|------------|
| `users` | id, name, email, password |
| `categories` | id, name (unique), description |
| `tasks` | id, title, description, status, priority, due_date, completed_at, category_id, assigned_to |

**Task statuses:** `pending` · `in_progress` · `completed` · `cancelled`

**Task priorities:** `low` · `medium` · `high`

## Routes

All routes require authentication.

```
GET    /dashboard

GET    /tasks
POST   /tasks
PATCH  /tasks/{task}
DELETE /tasks/{task}

GET    /categories
POST   /categories
PATCH  /categories/{category}
DELETE /categories/{category}

GET    /users
POST   /users
PATCH  /users/{user}
DELETE /users/{user}
```

## Testing

```bash
composer test
```

## Building for Production

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Project Structure

```
app/
├── Http/Controllers/   TaskController, CategoryController, UserController, DashboardController
├── Models/             Task, Category, User
├── Http/Requests/      Form request validation classes
└── Policies/           TaskPolicy, CategoryPolicy

database/
├── migrations/
├── factories/
└── seeders/

resources/views/        Blade templates (dashboard, tasks, categories, users, auth)
routes/
├── web.php             Main application routes
└── auth.php            Authentication routes
```

## Environment Variables

Key variables in `.env`:

```ini
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=sqlite        # or mysql

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log             # emails are written to log in development
```

## Documentation

| Document | Description |
|----------|-------------|
| [Requirements & User Stories (PDF)](docs/Landsat_UserStories_Gerald_Masaba_v2.pdf) | Full requirements specification — stakeholders, functional & non-functional requirements, user stories with acceptance criteria, sprint backlog, traceability matrix, and definition of done |

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
