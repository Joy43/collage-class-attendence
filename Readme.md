# AttApp — Student Attendance Management

Laravel 13 · Tailwind CSS 4 (Vite) · MySQL / TiDB Cloud

## Setup

```bash
composer install
npm install
cp .env.example .env && php artisan key:generate   # skip if .env already exists
# edit DB_* in .env (for TiDB Cloud also set MYSQL_ATTR_SSL_CA=/etc/ssl/cert.pem)

php artisan migrate --seed      # creates the tables + demo data
npm run build                   # or `npm run dev` while developing
php artisan serve               # http://localhost:8000
```

Demo login: `anika` / `123` (all seeded faculty accounts use password `123` — change them in Settings).

Tests: `php artisan test` (uses an in-memory SQLite database, never your real one).

## Folder structure

```
app/
├── Enums/                    AttendanceStatus, NotificationType (labels, emoji, Tailwind colour classes)
├── Http/
│   ├── Controllers/
│   │   ├── Auth/LoginController.php
│   │   ├── DashboardController.php
│   │   ├── AttendanceController.php          take attendance (form + save)
│   │   ├── AttendanceReportController.php    filterable report
│   │   ├── StudentController.php             list / create / update / delete
│   │   ├── NotificationController.php        page (HTML) + top-bar bell (JSON)
│   │   └── Settings/                         Settings tabs + Faculty / AcademicSession / Course CRUD
│   └── Requests/             Form Requests holding all validation rules
└── Models/                   Faculty (login user), Student, Course, AcademicSession,
                              Attendance, ActivityNotification
database/
├── migrations/               one file per table
└── seeders/DatabaseSeeder.php
resources/
├── css/app.css               Tailwind + theme tokens (light/dark)
├── js/                       app.js entry; theme, sidebar, modal, toast, crud-form, notifications…
│   └── pages/                loaded only on the page that needs them (dashboard charts, …)
└── views/
    ├── components/           <x-button>, <x-card>, <x-modal>, <x-avatar>, <x-icon>, <x-form.input> …
    │   └── layouts/          app.blade.php (sidebar + top bar), guest.blade.php
    ├── partials/             head, sidebar, topbar
    └── auth/ dashboard/ attendance/ reports/ students/ notifications/ settings/   one folder per feature
routes/web.php                every route, grouped by guest / auth
tests/Feature/                end-to-end tests
```

## Conventions

- **Colours are semantic tokens** (`bg-surface`, `text-muted`, `border-line`, `text-present`…) defined in
  `resources/css/app.css`; dark mode only swaps the variables, so views never need `dark:` colour overrides.
- **Reusable UI lives in Blade components** — change `<x-button>` once, every button follows.
- **Create/edit forms** (students, settings modals) share `resources/js/crud-form.js`: a button with
  `data-crud-edit` fills the form and switches it to `PUT`; `data-crud-create` resets it.
- **Notifications** are written with `ActivityNotification::record(NotificationType::…, $title, $message)`.
