# Skyrem Boilerplate

One-shot Composer package that overlays the **Skyrem Laravel 13 React boilerplate** onto a fresh Laravel application via:

```bash
php artisan skyrem:install
```

Inspired by [cleaniquecoders/kickoff](https://github.com/cleaniquecoders/kickoff), but delivered as an Artisan command for the React / Inertia / Fortify stack.

## Requirements

- PHP 8.4+
- A **fresh Laravel 13** app (recommended: React starter + Pest + npm)
- Composer

## Installation

```bash
laravel new myapp --react --pest --npm --no-interaction
cd myapp

composer require skyrem/boilerplate --dev
php artisan skyrem:install
```

The installer prompts for:

| Menu | Phase 1 options |
|------|-----------------|
| Frontend | React + Inertia + TypeScript + Tailwind 4 + Fortify + Wayfinder |
| Database | MySQL 8 + Redis via Laravel Sail |
| Dev tools | Skyrem full (Pint, Larastan, Rector, Pest Arch, Horizon, Telescope, Reverb, CI) |

Each menu currently has **one selectable option**. Additional stacks will be added later without changing the install UX.

Non-interactive (CI / sandbox):

```bash
php artisan skyrem:install --force --no-interaction
```

Useful flags:

- `--skip-packages` — skip `composer update` and `npm install`
- `--skip-npm` — skip npm only
- `--skip-migrate` — skip `migrate --seed`
- `--force` — skip overwrite confirmation

## After install

1. Review `.env` (from `.env.example` / `.env.dev.example`)
2. Start Sail: `./vendor/bin/sail up -d`
3. Remove the installer (one-shot):

```bash
composer remove skyrem/boilerplate --dev
```

## What gets installed

- Fortify auth (registration, verification, 2FA)
- Spatie Permission, Activity Log, Settings, Media Library, Query Builder
- CleaniqueCoders Traitify + Media Secure
- Horizon, Telescope, Reverb
- Users / Roles / Settings / Activity / Notifications admin UI (Inertia React)
- Pint, Larastan, Rector, Pest Arch, GitHub Actions, Sail compose templates

## Local development of this package

```bash
composer install
composer test
```

Sandbox against a real Laravel app (requires [Laravel installer](https://laravel.com/docs/installation)):

```bash
chmod +x bin/sandbox
bin/sandbox run
bin/sandbox reset
```

## Publishing to Packagist

1. Push this repository to GitHub (e.g. `skyrem/boilerplate`)
2. Create the package on [Packagist](https://packagist.org) and submit the repo URL
3. Tag a release (`v0.1.0`) so `composer require skyrem/boilerplate` resolves
4. Optional: enable GitHub Packagist sync webhook for auto-updates

Until published, consumers can path-require or use a VCS repository:

```bash
composer config repositories.skyrem-boilerplate \
  '{"type":"vcs","url":"https://github.com/skyrem/boilerplate"}'
composer require skyrem/boilerplate:dev-main --dev
```

## Phase 2 roadmap

- Additional frontend options (Livewire, API-only, etc.)
- Database menus beyond Sail MySQL/Redis
- Slim vs full tooling presets

## License

MIT. See [LICENSE.md](LICENSE.md).
