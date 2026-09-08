# Contributing to Timecrack

Thanks for taking the time to contribute! Timecrack is a self hosted time tracking application built with
Laravel 12 and Blade views, licensed under the GPLv3.

## Ways to contribute

* **Report a bug** – open an [issue](https://github.com/alextselegidis/timecrack/issues) with the steps to
  reproduce it, the version you are running and what you expected instead.
* **Request a feature** – open an issue describing the problem you are trying to solve, not only the solution
  you have in mind.
* **Send a pull request** – fix a bug, improve the documentation or implement a feature that was discussed in
  an issue first.
* **Report a vulnerability** – do not open an issue, follow [SECURITY.md](SECURITY.md) instead.

## Development setup

The application runs in Docker. The containers are named `timecrack-<service>-1`.

```bash
git clone https://github.com/alextselegidis/timecrack.git
cd timecrack
cp .env.example .env
docker compose up -d                                   # php-fpm, nginx, mysql, phpmyadmin, mailpit, swagger-ui
docker exec timecrack-php-fpm-1 sh -c 'cd /var/www/html && composer install'
docker exec timecrack-php-fpm-1 sh -c 'cd /var/www/html && php artisan migrate'
docker port timecrack-nginx-1                          # the host port nginx is published on
```

Log in with `admin@example.org` and the password `12345678`. Run
`php artisan db:seed --class=DemoSeeder` for a realistic demo dataset.

## Tests and formatting

Both run inside the container, because the host PHP binary is missing the `dom` and `xml` extensions. The
test suite runs against the MySQL `testing` database and cannot run on SQLite.

```bash
docker exec timecrack-php-fpm-1 sh -c 'cd /var/www/html && ./vendor/bin/phpunit'
docker exec timecrack-php-fpm-1 sh -c 'cd /var/www/html && ./vendor/bin/pint'
```

Please make sure that the tests pass and that Pint reports no changes before you open a pull request.

## Coding conventions

* Every PHP, Blade, CSS and JS file starts with the project header comment block. Copy it from a neighbouring
  file when you create a new one.
* Four space indentation, LF line endings, a final newline and a maximum line length of 120 characters, in
  code, Markdown and commit messages alike.
* All user facing strings go through `__()` with a `snake_case` key defined in `lang/en.json`.
* Styling builds on Bootstrap utility classes. Custom CSS belongs in `public/styles/timecrack.css` and uses
  the `--tc-*` design tokens; avoid introducing new colours.
* Durations are whole minutes everywhere and are formatted only through `duration_label()` and
  `duration_hours()` in `helpers.php`.
* Add a `CHANGELOG.md` entry for anything user visible.

## Pull requests

1. Fork the repository and create your branch from `main`.
2. Keep the change focused; one topic per pull request.
3. Describe what changed and why, and link the related issue.
4. By contributing you agree that your work is licensed under the GPLv3.
