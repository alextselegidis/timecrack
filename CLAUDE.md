# Timecrack

Self-hosted time tracking application. Laravel 12 with Blade views, GPLv3.

Users track time per project with a single running timer, review and correct their history, and export it as
CSV. Administrators additionally manage projects, users and localization under `/setup`. A token authenticated
REST API lives under `/api/v1` (Sanctum plus `tailflow/laravel-orion`).

## Working agreements

- **Never create a git branch** unless explicitly asked to. Commit to the branch that is already checked out.
- **Never add Claude attribution to commits.** No `Co-Authored-By: Claude ...` trailer, no
  "Generated with Claude Code" line, no mention of AI assistance in commit messages or pull request bodies.
- Only commit or push when asked.
- Keep every line of a `CHANGELOG.md` entry at 120 characters or shorter, wrapping longer entries onto
  indented continuation lines.

## Environment

The application runs in Docker. The containers are named `timecrack-<service>-1`:

```bash
docker compose up -d                                  # php-fpm, nginx, mysql, phpmyadmin, mailpit, swagger-ui
docker port timecrack-nginx-1                          # the host port nginx is published on
docker exec timecrack-php-fpm-1 sh -c 'cd /var/www/html && php artisan migrate'
docker exec timecrack-php-fpm-1 sh -c 'cd /var/www/html && ./vendor/bin/phpunit'
docker exec timecrack-php-fpm-1 sh -c 'cd /var/www/html && ./vendor/bin/pint'
```

The host PHP binary is missing the `dom`/`xml` extensions, so PHPUnit and Composer only work inside the
container.

Tests run against the MySQL database `testing`, which `docker/mysql-init/01-create-testing-database.sql`
creates on a fresh MySQL volume. They cannot run on SQLite, because several data migrations use MySQL only SQL
such as `TIMESTAMPDIFF`.

`database/seeders/DemoSeeder.php` fills an instance with realistic demo data. The default administrator from
the migrations is `admin@example.org` with the password `12345678`.

## Layout

- `app/Http/Controllers` – one controller per page, plus `Api/V1` controllers for the REST API.
- `app/Auth/AppSessionGuard.php` – session guard that suffixes the "remember me" cookie with a hash of
  `APP_KEY`, so two installations on the same domain cannot log each other out. `config/session.php` does the
  same for the session cookie. Keep both in sync.
- `app/Http/Middleware/ExtendRememberSession.php` – extends the session lifetime for remembered users. The
  lifetime lives in `config/auth.php` under `guards.web.remember` and must stay within the 400 days that
  browsers accept for a persistent cookie.
- `helpers.php` – globally autoloaded helpers, currently `sort_link()` and `setting()`.
- `resources/views/layouts` – `main-layout`, `auth-layout` and `message-layout`, all including
  `resources/views/shared/head.blade.php`. Add anything that belongs in `<head>` there once, not per layout.
- `resources/views/shared` – reusable partials (navigation, sidebars, value formatters).
- `public/styles/timecrack.css` and `public/scripts/timecrack.js` – the application stylesheet and script.
  They are plain files served straight from `public/`, **not** built by Vite. `resources/css` and
  `resources/js` are Laravel leftovers and are unused.
- `public/vendor` – vendored Bootstrap 5.3, Bootstrap Icons and Pace, also served straight from `public/`.
- `public/manifest.json`, `public/sw.js`, `public/offline.html` – the PWA. The service worker names its cache
  after the `?v=` parameter of its registration URL, which carries `config('app.version')`.

## Conventions

- Every PHP, Blade, CSS and JS file starts with the project header comment block. Copy it from a neighbouring
  file when creating a new one.
- Four space indentation, LF line endings, final newline (see `.editorconfig`). Format PHP with Pint.
- All user facing strings go through `__()` with a `snake_case` key defined in `lang/en.json`.
- Styling builds on Bootstrap utility classes. Custom CSS belongs in `public/styles/timecrack.css`, which
  defines its design tokens as `--tc-*` custom properties in `:root` and overrides Bootstrap through
  `--bs-*` variables. The palette is cinnamon orange (`--tc-brand`) plus dark (`--tc-dark`); avoid
  introducing new grays.
- Bump `config('app.version')` for a release. It busts the query strings of the stylesheet and script and
  invalidates the service worker cache.
- The tables of the application render as one card per row on phones. The cell labels come from the table head
  and are attached by `public/scripts/timecrack.js`, so a new table needs a `<thead>` but no extra markup.
