# Running AttApp

**Prerequisites:** PHP 8.3+ (you have 8.5), Node and npm. Composer isn't installed system-wide. I used a copy in the scratchpad, but `vendor/` is already installed in the project.

## 1. First-time setup
```bash
cd "/Volumes/ss joy/dcc/attapp"
```
- `vendor/` and `node_modules/` are installed, `.env` exists with your TiDB settings, and `public/build` is already compiled.
- To reinstall on another machine, run `composer install`, `npm install`, then `cp .env.example .env && php artisan key:generate`.

## 2. Create the tables
```bash
php artisan migrate --seed
```
- This creates the 6 tables in the database named in `.env` (`DB_DATABASE=sys` on TiDB) and adds the demo data.
- `sys` is a TiDB/MySQL system schema. If the migration is refused or you'd prefer a clean one, create a database such as `attapp` in TiDB, set `DB_DATABASE=attapp` in `.env`, then migrate.
- To rebuild from scratch, run `php artisan migrate:fresh --seed`. That wipes the tables.

## 3. Start the app
```bash
php artisan serve
```
Open http://localhost:8000. Log in as `anika` / `123`. All the seeded accounts use password `123`.

## 4. While editing views, CSS or JS
Use the Vite dev server in a second terminal, so changes show up live:
```bash
npm run dev
```
Before you deploy, run `npm run build` and stop the dev server.

## 5. Tests
```bash
php artisan test
```
These use an in-memory SQLite database, never your TiDB data.

## Troubleshooting
| Symptom | Fix |
|---|---|
| `Access denied` | Add your current IP to the TiDB Cloud allowlist, or reset the password in `.env` (quote it if it has special characters). |
| Page shows unstyled | Run `npm run build`, or start `npm run dev`. |
| `Table … doesn't exist` | Run step 2. |
| Odd PHP parse errors in `config/` | Delete the macOS junk files: `find . -name '._*' -not -path './vendor/*' -not -path './node_modules/*' -delete` |
| Changed `.env` but nothing happens | Run `php artisan config:clear`. |