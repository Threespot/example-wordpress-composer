# Pantheon WordPress Composer: Repo Comparison & Recommendation

Summary of a comparison across three repositories:

1. `pantheon-systems/example-wordpress-composer` (the original Pantheon reference)
2. `pantheon-systems/wordpress-composer-managed` (Pantheon's newer Integrated-Composer upstream)
3. `Threespot/example-wordpress-composer` (Threespot's fork of #1)

---

## 1. The two Pantheon repos

| | `example-wordpress-composer` | `wordpress-composer-managed` |
|---|---|---|
| Workflow | **External CI** (CircleCI/GitLab/Bitbucket) builds artifact, pushes to Pantheon | **Integrated Composer** — Pantheon builds on-platform |
| Distribution | One-time scaffold via Terminus Build Tools Plugin | Pantheon **Upstream** (`terminus site:create ... "WordPress (Composer Managed)"`) |
| Foundation | Custom Pantheon layout (Bedrock-inspired) + `pantheon-systems/wordpress-composer` | **Roots/Bedrock** (`roots/wordpress`, `roots/wp-config`, autoloader, etc.) |
| Content dir | `web/wp-content/{plugins,themes}/` | `web/app/{mu-plugins,plugins,themes}/` (Bedrock) |
| PHP | `>=7.3` (platform pinned 7.3) | `>=8.0` |
| Default branch | `master` | `default` |
| Support badge | Actively Maintained | **Minimal Support / Early Access** |
| Activity (May 2026) | 99 ★ / 49 forks · last push 2026-03-31 | 8 ★ / 10 forks · last push 2026-02-09 |

### Architectural differences

- **Build model.** `example-wordpress-composer` is a *reference implementation* you copy once: ships substantial `.ci/`, `.circleci/`, `.gitlab-ci.yml`, `bitbucket-pipelines.yml`, visual-regression and PHPUnit scaffolding, plus `build-providers.json`. You own and maintain that pipeline. `wordpress-composer-managed` has none of that — it expects Pantheon's Integrated Composer to run `composer install` on push, and instead carries `upstream-configuration/`, `pantheon.upstream.yml`, and `.lando.upstream.yml`.
- **Config style.** Example uses a custom `wp-config.php` with `vlucas/phpdotenv` ^3. Managed uses Bedrock: `.env.example`, `.env.pantheon`, `config/environments/{development,staging,production}.php`, `oscarotero/env`, `roots/wp-config`.
- **Extensibility.** Managed adds `cweagans/composer-patches` (with `enable-patching: true`), an `upstream-require` script for custom-upstream package management, and `pantheon-systems/pantheon-mu-plugin`. The example has none of these.
- **Tooling.** Example: WPCS 1.x, PHPUnit 7, `brain/monkey`. Managed: `pantheon-systems/pantheon-wp-coding-standards`, shellcheck, `roave/security-advisories: dev-latest`.

### Which to pick (greenfield)

- **`wordpress-composer-managed`** for new sites — Pantheon's current recommendation, real upstream that keeps receiving updates, simpler ops (no CI to maintain), Bedrock conventions.
- **`example-wordpress-composer`** only if you specifically need an external CI pipeline (custom test gates, multi-provider deploys, visual regression, multidev orchestration via CI).

The two are not interchangeable: folder layout, env handling, and deploy mechanics differ — migrating between them is a real project, not a swap.

---

## 2. Threespot's fork

`Threespot/example-wordpress-composer` is **38 commits ahead, 24 commits behind** the upstream. It's no longer just a fork — it's an opinionated Threespot WordPress starter:

- **PHP 8.2**, `roots/wordpress` ~6.8 (so already pulling Bedrock's WP installer)
- Already uses many Bedrock-adjacent packages: `roots/wp-config`, `roots/bedrock-autoloader`, `roots/bedrock-disallow-indexing`, `roots/acorn`, `oscarotero/env`, `vlucas/phpdotenv` ^5, `pantheon-systems/pantheon-mu-plugin`
- Sage theme at `web/wp-content/themes/sage` with yarn dev/build wired into CircleCI
- ACF Pro via `wpengine/advanced-custom-fields-pro` (private composer repo + ACF license CircleCI context)
- ~30 curated plugins (Yoast, Query Monitor, Redirection, FileBird, PublishPress, etc.)
- `quicksilver-pushback` for Pantheon → GitHub round-trips
- Custom Composer scripts: `deploy:test/live/all`, `theme:dev/build`, `replace-fixme`, `collect-logs`
- Tooling: `.tool-versions` (asdf), `.tinkerwell`, `.op` (1Password), custom `cleanup-composer`, `ScriptHandler.php`
- Full CircleCI pipeline with `build_php`, theme composer install, multidev creation, GitHub commit comments

In short: the fork has already absorbed many of Bedrock's *dependencies* without adopting Bedrock's *layout*.

---

## 3. Should Threespot switch to `wordpress-composer-managed`?

### Pros of switching

1. **Drop CI build pipeline maintenance for the PHP side.** Pantheon's Integrated Composer runs `composer install` on push — no more debugging build-tools-ci docker images, multidev creation scripts, or `set-environment` sourcing.
2. **Bedrock-native, not Bedrock-bolted-on.** `web/app/` layout, `config/environments/`, proper `.env`/`.env.pantheon`/`.env.local` separation. The fork is roughly halfway there already.
3. **Upstream updates flow in.** It's a real Pantheon Upstream — security and infrastructure changes land via `terminus upstream:updates:apply`. The fork currently has 24 commits to evaluate.
4. **Patching infrastructure built in** (`cweagans/composer-patches`).
5. **Less custom plumbing to maintain.** `ScriptHandler.php`, `cleanup-composer`, `prepareForPantheon`, the `.ci/` tree — most of that exists because Integrated Composer didn't when the fork was built.
6. **Aligned with Pantheon's recommended path.** New docs, examples, and support resources will assume this stack.

### Cons / real costs

1. **Folder rename is disruptive.** `web/wp-content/{plugins,themes,mu-plugins}` → `web/app/{plugins,themes,mu-plugins}`. Existing media URLs become `/app/uploads/...`. For live sites this means redirects, hardcoded path audits, and possibly SEO impact unless you preserve `/wp-content/` via rewrites (which fights the upstream's design).
2. **Sage theme build still needs CI.** Integrated Composer **does not run Node/yarn**. CircleCI doesn't actually go away — still needed to build Sage assets and either commit them or push to Pantheon. You lose the Composer step but keep the asset pipeline.
3. **Support tier downgrade in writing.** `wordpress-composer-managed`'s badge says **"Minimal Support"** and the README explicitly calls it **Early Access**. `example-wordpress-composer` is **"Actively Maintained."**
4. **Loss of fork features.** Re-apply or replace: `quicksilver-pushback`, ACF Pro context, `deploy:*` scripts, multidev/PR comment automation, `replace-fixme` scaffolding, tinkerwell driver, log collection.
5. **Migration is a real project.** Plus 38 commits of customizations need triage: which are still needed, which are superseded by the new upstream, which need re-implementation.
6. **Curated plugin list lives in `composer.json`.** Portable, but a custom-upstream route expects project-specific plugin requires to live in the site repo, not the upstream.

### Impact on the CircleCI workflow

| CircleCI piece | Fate after switching |
|---|---|
| `build_php` (composer install) | **Redundant** — Integrated Composer does this on Pantheon |
| `static_tests` (phpcs etc.) | Keep — still useful as a PR gate |
| `push_to_pantheon` / multidev creation | **Mostly redundant** — Integrated Composer creates multidevs automatically on git push |
| Sage `yarn build` | **Keep, mandatory** — Pantheon does not run Node |
| GitHub commit comments / ACF license | Keep if still wanted; nothing in the upstream provides them |
| `quicksilver-pushback` | Replace or drop — not bundled in managed |
| Visual regression / Behat | Keep, decoupled from upstream choice |

CircleCI **shrinks but does not disappear**. It would run primarily for asset builds + static tests + deploys, with Pantheon handling PHP dependency installation.

---

## 4. Recommendation

**Don't migrate existing live sites just to switch upstreams.** The folder rename + media URL change is a real cost, and the Sage build means you don't get the "no more CI" payoff anyway.

**Do consider it for a fresh greenfield project** where:
- Starting clean (no media URL legacy)
- OK with Early Access / Minimal Support tier
- Team wants Pantheon to own the build

### Middle path (recommended for Threespot)

Keep the fork, but cherry-pick the modern bits from `wordpress-composer-managed`:

1. **Adopt `config/environments/` pattern** for cleaner per-environment config (`development.php`, `staging.php`, `production.php`) instead of branching logic in `wp-config.php`.
2. **Add `cweagans/composer-patches`** so plugin patches can be applied in a tracked, reproducible way instead of post-install hacks.
3. **Borrow the `upstream-configuration/` pattern** if Threespot ever wants to manage shared dependencies across multiple sites — useful for keeping a baseline plugin set in sync.
4. **Pull in `pantheon-systems/pantheon-wp-coding-standards`** to replace the older WPCS 1.x setup.
5. **Consider `pantheon-systems/pantheon-mu-plugin`** if not already used — the fork has it, just verify it's wired up.
6. **Periodically rebase on upstream** `pantheon-systems/example-wordpress-composer` — currently 24 commits behind. Schedule a quarterly catch-up to avoid drift.
7. **Keep `quicksilver-pushback`** — it's a real feature the managed upstream doesn't replace.
8. **Document the fork as a Threespot starter**, not "a fork of pantheon-systems." It has diverged enough that treating it as a first-class internal template (with its own README, conventions, plugin rationale) is more honest than positioning it as a fork.

The fork has matured into something that fits Threespot's projects well. Switching would be a sideways move with non-trivial migration cost — not a clear upgrade.
