# Repository Guidelines

## Project Structure & Module Organization
This repository is a Laravel 13 application on PHP 8.3. Core backend code lives in `app/`, with controllers under `app/Http/Controllers`, middleware in `app/Http/Middleware`, models in `app/Models`, and enums in `app/Enums`. HTTP routes are defined in `routes/web.php`; there is no separate API route file in this checkout. Blade views and frontend assets live in `resources/views`, `resources/css`, and `resources/js`. Database factories, migrations, and seeders are under `database/`. Tests are split into `tests/Feature` and `tests/Unit`.

## Build, Test, and Development Commands
Use Composer for backend tasks and npm for frontend assets.

- `composer setup`: install PHP and Node dependencies, create `.env`, generate the app key, run migrations, and build assets.
- `composer dev`: run the local Laravel server, queue listener, log tailing, and Vite dev server together.
- `composer test`: clear config and run the PHPUnit suite through Laravel.
- `npm run dev`: start the Vite frontend watcher only.
- `npm run build`: create a production asset build.

## Coding Style & Naming Conventions
Follow `.editorconfig`: UTF-8, LF endings, spaces for indentation, and 4-space indents for PHP and most project files. Keep PHP classes PSR-4 compliant and match file names to class names, for example `ArtistProfileController.php` or `WorkspaceItem.php`. Use `PascalCase` for classes and enums, `camelCase` for methods and variables, and snake_case only where Laravel expects it, such as database columns or migration names. Format PHP with `./vendor/bin/pint`.

## Testing Guidelines
PHPUnit 12 is configured in `phpunit.xml`, with `tests/Unit` and `tests/Feature` as separate suites. Tests run against in-memory SQLite, so keep them isolated and deterministic. Name tests `*Test.php` and place request or route behavior in Feature tests, with pure domain logic in Unit tests. Run `composer test` before opening a PR.

## Commit & Pull Request Guidelines
Git history is not available in this workspace, so no repository-specific commit convention could be verified. Use concise, imperative commit subjects such as `Add conversation notification filtering`. PRs should summarize the user-visible change, note any migration or environment impacts, link the relevant issue, and include screenshots for Blade or UI updates.

## Security & Configuration Tips
Do not commit `.env` or generated secrets. Prefer environment variables for credentials and service keys. When changing queues, mail, or database behavior, document the required `.env` values in the PR.
