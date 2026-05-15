# CLAUDE.md

Project-specific notes for Claude Code. Read alongside `README.md` — this file covers what the README doesn't.

This repo is Threespot's fork of `pantheon-systems/example-wordpress-composer`. Don't assume upstream parity; Threespot-specific customizations are layered throughout (mu-plugins, config split, composer requirements, theme).

## General Behavior

When answering WordPress/PHP questions, provide a direct answer first before exploring the codebase. Only investigate files if the user asks for project-specific context or if the question genuinely requires it.

## Code Style / Conventions

Never introduce Unicode smart quotes or curly quotes in any code file (PHP, JS, SCSS, Blade, JSON, etc.). Always use straight quotes (' and "). Do not alter existing comments solely to replace curly quotes with straight quotes — only avoid introducing new ones. A PostToolUse hook in `.claude/settings.json` scans added lines after every Edit and will block if any contain curly quotes.

## Config layout

`web/wp-config.php` is intentionally minimal — it requires `config/application.php` and `wp-settings.php`, nothing else. Actual configuration lives in:

- `config/application.php` — shared settings (DB, URLs, salts, defines, Object Cache Pro). Uses `Roots\WPConfig\Config` + `oscarotero/env`. Always edit here, not in `wp-config.php`.
- `config/environments/development.php` — local + Pantheon dev/multidev
- `config/environments/staging.php` — Pantheon `test`
- `config/environments/production.php` — Pantheon `live`

Pantheon's `PANTHEON_ENVIRONMENT` is mapped to a logical `WP_ENV` near the top of `application.php`:

| `PANTHEON_ENVIRONMENT` | `WP_ENV`      | Env file loaded   |
|------------------------|---------------|-------------------|
| `live`                 | `production`  | `production.php`  |
| `test`                 | `staging`     | `staging.php`     |
| anything else (dev, multidev, local, CLI) | `development` | `development.php` |

The selected env file is required after defaults are set, then `Config::apply()` runs once at the end. Any new define should go in `application.php` (cross-env) or the appropriate env file (env-specific) — never both.

## Environment variables

Locally, `.env` at the repo root is loaded by phpdotenv (required: `DB_NAME`, `DB_USER`, `DB_HOST`). On Pantheon, env vars are injected automatically and `.env` is skipped.

Licenses / tokens read from env:
- `ACF_PRO_LICENSE` — Advanced Custom Fields Pro
- `ACP_LICENCE` — Admin Columns Pro (note British spelling)
- `SEARCHWP_LICENSE_KEY` — SearchWP
- `OCP_LICENSE` — Object Cache Pro (on Pantheon, also read from `~/files/private/secrets.json`; under Lando, read from `auth.json`)

`auth.json` lives at repo root and holds HTTP basic creds for the private Composer repos (objectcache.pro, ACF, Admin Columns). It is gitignored — never commit it.

## Composer repositories and required tooling

Beyond Packagist, `composer.json` registers:
- `https://objectcache.pro/repo/` (Object Cache Pro)
- `https://connect.advancedcustomfields.com` (ACF Pro)
- `https://composer.admincolumns.com` (Admin Columns Pro)
- `https://repo.wp-packages.org` (community WP plugin mirror — most `wp-plugin/*` deps come from here)
- VCS: `github.com/threespot/wp-base-config`

Platform: **PHP 8.3** (pinned in `composer.json` config.platform). `.tool-versions` pins Node 24.13.0 and Yarn 1.22.17 (asdf-compatible).

## Composer scripts (run via `composer <script>`)

- `phpstan` — static analysis (see README for setup)
- `phpcs` / `phpcbf` — coding standards (config in `phpcs.xml.dist`, uses `pantheon-systems/pantheon-wp-coding-standards`)
- `unit-test` — runs `phpunit tests/unit/*`
- `lint` — `php -l` syntax check across `web/wp-content`
- `theme:dev` / `theme:build` — proxies `yarn start` / `yarn build` inside `web/wp-content/themes/sage`
- `replace-fixme` — fills in `fixme-app-name` placeholders in `deploy:*` scripts (run once per new project)
- `deploy:test` / `deploy:live` / `deploy:all` — Terminus wrappers; the `fixme-app-name.{env}` site name must be replaced before use
- `mu-stubs:update` — re-fetches `loader.php` and `bedrock-autoloader.php` from upstream (see `extra.mu-plugin-stubs` in `composer.json`)
- `cleanup` — runs `scripts/composer/cleanup-composer` (fires on post-install/update/create-project)

## mu-plugins

`web/wp-content/mu-plugins/` mixes Composer-installed packages with checked-in custom files. The `.gitignore` allow-list (lines ~58–64) is what controls which files are tracked:

Tracked (custom, do not delete):
- `loader.php` — boots `pantheon-mu-plugin/pantheon.php` and makes mu-plugins visible in `wp-admin/plugins.php`. Sourced from upstream Pantheon; refresh via `composer mu-stubs:update`.
- `bedrock-autoloader.php` — auto-loads any standalone `.php` files dropped into `mu-plugins/`. Sourced from Bedrock; refresh via the same script.
- `threespot-wp-base-config.php` — bootstraps the `threespot/wp-base-config` package.
- `suppress-admin-notices.php` — hides 3rd-party plugin admin notices (added intentionally).
- `disable-polylang-cookie.php` — disables Polylang's cookie.
- `custom-post-types/` and `custom-taxonomies/` — convention-based registration via `threespot/wp-base-config` (see README "Custom Post Types and Taxonomies").

Installed by Composer (do not edit in place — change `composer.json`):
- `pantheon-mu-plugin/`
- `bedrock-disallow-indexing/`

## Theme

Active theme is **Sage** at `web/wp-content/themes/sage` (Roots Acorn-based, Laravel-flavored, Blade views, yarn-built). `twentytwentyfive` is shipped as a fallback. Sage has its own `composer.json` and `package.json`:
- PHPStan needs Sage's vendor dir installed (`cd web/wp-content/themes/sage && composer install`) — README covers this.
- Front-end work: `composer theme:dev` for HMR; `composer theme:build` for production assets.
- PHPStan only analyzes `app/`, `config/`, `functions.php`, `index.php`. Blade views in `resources/views/` are excluded.

## `.gitignore` strategy (and the "cut" line)

`web/` is aggressively negated: everything under it is ignored except explicit allow-listed paths (see the `!web/...` lines). When adding a new committed file under `web/`, add a matching `!` rule or it will be silently dropped.

The file is split by a `# :::::::::::::::::::::: cut ::::::::::::::::::::::` marker:
- **Above the cut**: ignored in GitHub but committed to the Pantheon repo (built artifact). This is how vendored Composer output reaches Pantheon.
- **Below the cut**: ignored everywhere (logs, OS files, archives).

The CI build job (`.ci/build/php`) installs Composer deps so the Pantheon-pushed artifact contains the `vendor/` and `web/wp/` paths that are gitignored from GitHub.

## Deploy placeholders

The `deploy:test`, `deploy:live`, and `deploy:all` Composer scripts contain `fixme-app-name` literals. New projects should run `composer replace-fixme` once after scaffolding. If you see deploy commands failing with "site not found," check whether the placeholder was ever replaced.

## Misc

- `README.template.md` is the upstream Pantheon README, kept for reference. `README.md` is the active, customized version.
- `pantheon-wp-composer-comparison.md` is project notes comparing this starter to alternatives; not user-facing docs.
- `docs/troubleshooting.md` has a few known-issue notes.
- `.op/` and `.tinkerwell/` are local-tool directories (1Password, Tinkerwell). Not part of the build.
- `object-cache.php` in `wp-content/` is the OCP dropin; it's allow-listed in `.gitignore` even though installed via Composer.
