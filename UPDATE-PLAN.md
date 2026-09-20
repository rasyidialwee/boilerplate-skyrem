# BOILERPLATE13 UPDATE PLAN
## Audit & Migration — 20 Sept 2026

> **Tujuan:** Update boilerplate13 ke latest stable versions sebelum digunakan untuk project baru.
> **Repo:** rasyidialwee/boilerplate13 (last commit: 2026-05-04)
> **Status:** DRAFT — untuk review & approval sebelum execute.

---

## 1. RINGKASAN AUDIT

| Kategori | Status | Prioriti |
|---|---|---|
| Laravel Framework | ✅ Latest (13.32.0) | — |
| Inertia Laravel | ⚠️ **Major behind** (^2.0 → v3.3.4) | 🔴 HIGH |
| Spatie Permission | ⚠️ **Major behind** (^6.9 → 8.3.0) | 🔴 HIGH |
| Laravel Fortify | ⚠️ Minor behind (^1.30 → v1.39.0) | 🟡 MEDIUM |
| Laravel Horizon | ⚠️ Minor behind (^5.23 → v5.49.0) | 🟡 MEDIUM |
| Laravel Reverb | ⚠️ Minor behind (^1.0 → v1.11.1) | 🟡 MEDIUM |
| Laravel Wayfinder | ⚠️ Patch behind (^0.1.9 → v0.1.21) | 🟢 LOW |
| Vite | ⚠️ **Major behind** (^7.0.4 → 8.3.0) | 🔴 HIGH |
| TypeScript | ⚠️ **2 Major behind** (^5.7.2 → 7.0.2) | 🔴 HIGH |
| React | ✅ Latest (^19.2.0 → 19.3.0) | — |
| Tailwind CSS | ✅ Latest (^4.0.0 → 4.3.3) | — |
| Docker (PHP) | ⚠️ 8.4 available (currently 8.3) | 🟡 MEDIUM |
| Docker (Node) | ✅ 22 (LTS) | — |

---

## 2. CRITICAL UPDATES (Major Version Changes)

### 2.1 Inertia Laravel v2 → v3
**Current:** `inertiajs/inertia-laravel: ^2.0`
**Target:** `inertiajs/inertia-laravel: ^3.0`

**Breaking Changes (v3):**
- Requires Laravel 11+ (✅ kita guna 13)
- New `Inertia::encryptHistory()` API
- Changed deferred props API
- `Inertia::share()` now accepts closures only
- Removed deprecated `Inertia::setRootView()`

**Migration Steps:**
1. Update composer.json: `^3.0`
2. Run `composer update inertiajs/inertia-laravel`
3. Review all `Inertia::share()` calls — convert arrays to closures
4. Check any custom Inertia responses
5. Test auth flow (Fortify integration)

**Files to check:**
- `app/Http/Middleware/HandleInertiaRequests.php`
- Any service providers sharing data
- Controllers returning Inertia responses

---

### 2.2 Spatie Permission v6 → v8
**Current:** `spatie/laravel-permission: ^6.9`
**Target:** `spatie/laravel-permission: ^8.0`

**Breaking Changes (v7 → v8):**
- v7: Requires Laravel 10+ (✅ kita guna 13)
- v8: Requires PHP 8.2+ (✅ kita guna 8.3/8.4)
- Changed `PermissionRegistrar` cache handling
- `hasRole()`/`hasPermissionTo()` return types stricter
- Removed deprecated `hasAnyRole()` variadic syntax

**Migration Steps:**
1. Update composer.json: `^8.0`
2. Run `composer update spatie/laravel-permission`
3. Clear permission cache: `php artisan permission:cache-reset`
4. Review role/permission checks in policies & controllers
5. Re-run seeders if needed

**Files to check:**
- `app/Policies/` — all policy files
- `app/Models/User.php` — HasRoles trait
- `database/seeders/RolePermissionSeeder.php`

---

### 2.3 Vite v7 → v8
**Current:** `vite: ^7.0.4`
**Target:** `vite: ^8.0.0`

**Breaking Changes (v8):**
- Requires Node 20.19+ or 22.12+ (✅ kita guna 22)
- Rolldown becomes default bundler (experimental)
- `build.rollupOptions` → `build.rolldownOptions`
- HMR API changes
- Plugin compatibility — check all vite plugins

**Migration Steps:**
1. Update package.json: `vite: ^8.0.0`
2. Update `@vitejs/plugin-react`: check v6 compatibility
3. Update `laravel-vite-plugin`: check compatibility
4. Run `npm install`
5. Test dev server & build
6. Review vite.config.ts for deprecated options

**Files to check:**
- `vite.config.ts`
- `package.json` scripts
- Any custom vite plugins

---

### 2.4 TypeScript v5 → v7
**Current:** `typescript: ^5.7.2`
**Target:** `typescript: ^7.0.0`

**Breaking Changes (v6 → v7):**
- v6: New `tsgo` native compiler (10x faster)
- v7: Decorators become stable (legacy decorators deprecated)
- `strict` mode changes
- New `satisfies` operator improvements

**Migration Steps:**
1. Update package.json: `typescript: ^7.0.0`
2. Update `typescript-eslint`: check compatibility
3. Update `@types/*` packages
4. Run `npx tsc --noEmit` — fix any type errors
5. Test build

**Files to check:**
- `tsconfig.json` — strictness settings
- All `.ts` / `.tsx` files
- Type definition files

---

## 3. MEDIUM PRIORITY UPDATES

### 3.1 Laravel Fortify v1.30 → v1.39
- Security patches & bug fixes
- No breaking changes expected
- Update: `composer update laravel/fortify`

### 3.2 Laravel Horizon v5.23 → v5.49
- Bug fixes, UI improvements
- No breaking changes expected
- Update: `composer update laravel/horizon`

### 3.3 Laravel Reverb v1.0 → v1.11
- WebSocket improvements
- No breaking changes expected
- Update: `composer update laravel/reverb`

### 3.4 PHP 8.3 → 8.4 (Docker)
**Current:** `sail-8.4/app` (dev), `php:8.4-fpm` (prod Dockerfile)
**Issue:** composer.json says `^8.3` but Docker uses 8.4

**Action:**
- Update composer.json: `"php": "^8.4"`
- Verify all packages support 8.4
- Test Sail + production build

---

## 4. LOW PRIORITY UPDATES

| Package | Current | Latest | Notes |
|---|---|---|---|
| laravel/wayfinder | ^0.1.9 | v0.1.21 | Type generation improvements |
| spatie/laravel-medialibrary | ^11.22 | 11.23.8 | Bug fixes |
| spatie/laravel-settings | ^3.5 | 3.9.0 | Bug fixes |
| spatie/laravel-activitylog | ^4.10 | (check) | — |
| spatie/laravel-query-builder | ^6.3 | (check) | — |
| laravel/telescope | ^5.11 | (check) | — |

**Action:** `composer update` (no code changes needed)

---

## 5. NODE PACKAGES — BATCH UPDATE

### Safe to update (minor/patch):
```bash
npm update react react-dom @types/react @types/react-dom
npm update tailwindcss @tailwindcss/vite
npm update react-hook-form @hookform/resolvers
npm update zod
npm update @radix-ui/* (all)
npm update lucide-react
npm update eslint prettier typescript-eslint
npm update laravel-echo pusher-js @laravel/echo-react
```

### Needs review (major):
```bash
# Check compatibility first
npm install vite@^8.0.0
npm install typescript@^7.0.0
npm install @vitejs/plugin-react@latest
npm install @inertiajs/react@latest
```

---

## 6. DOCKER UPDATES

### 6.1 Development (compose-dev.yaml)
- Currently uses `sail-8.4/app` — ✅ OK
- Check if Sail 1.47+ supports PHP 8.4 fully

### 6.2 Production (deployment/Dockerfile)
- Currently `php:8.4-fpm` — ✅ OK
- Check `node:22` — ✅ LTS

### 6.3 Nginx
- `nginx:latest` — consider pinning version (e.g., `nginx:1.27-alpine`)

---

## 7. EXECUTION PLAN

### Phase 1: Preparation (30 min)
```bash
# 1. Create branch
git checkout -b update/major-deps-2026-09

# 2. Backup current state
cp composer.json composer.json.backup
cp package.json package.json.backup

# 3. Clear caches
rm -rf vendor node_modules
```

### Phase 2: PHP Updates (1 hour)
```bash
# 1. Update composer.json
#    - inertiajs/inertia-laravel: ^3.0
#    - spatie/laravel-permission: ^8.0
#    - php: ^8.4
#    - All other packages to latest

# 2. Update dependencies
composer update

# 3. Fix breaking changes
#    - Inertia::share() closures
#    - Permission cache reset
#    - Test auth flow

# 4. Run tests
php artisan test
```

### Phase 3: Node Updates (1 hour)
```bash
# 1. Update package.json
#    - vite: ^8.0.0
#    - typescript: ^7.0.0
#    - @vitejs/plugin-react: latest
#    - All others to latest

# 2. Update dependencies
npm install

# 3. Fix breaking changes
#    - vite.config.ts
#    - TypeScript errors
#    - Build test

# 4. Test build
npm run build
npm run dev
```

### Phase 4: Integration Test (30 min)
```bash
# 1. Full stack test
sail up -d
sail artisan migrate:fresh --seed
sail test

# 2. Frontend test
sail npm run build
sail npm run dev

# 3. Manual smoke test
#    - Login/register
#    - Dashboard loads
#    - Roles/permissions work
#    - File upload (MediaLibrary)
```

### Phase 5: Documentation & Merge (15 min)
```bash
# 1. Update CHANGELOG.md
# 2. Update README.md (version numbers)
# 3. Commit
git add .
git commit -m "Update major dependencies: Inertia v3, Permission v8, Vite v8, TS v7"
# 4. Push & PR
git push origin update/major-deps-2026-09
```

---

## 8. RISKS & MITIGATION

| Risiko | Mitigasi |
|---|---|
| Inertia v3 breaking auth flow | Test Fortify integration thoroughly; rollback plan ready |
| Permission v8 cache issues | Clear cache after update; re-seed if needed |
| Vite v8 plugin incompatibility | Check all vite plugins before update; test build |
| TypeScript v7 type errors | Run `tsc --noEmit` incrementally; fix before commit |
| Docker build fails | Test both dev (Sail) and prod (Dockerfile) builds |
| Test suite fails | Fix tests before merge; don't skip |

---

## 9. CHECKLIST SEBELUM MERGE

- [ ] `composer update` completes without errors
- [ ] `npm install` completes without errors
- [ ] `php artisan test` passes
- [ ] `npm run build` succeeds
- [ ] `npm run dev` starts without errors
- [ ] Sail up + migrate + seed works
- [ ] Login/register works
- [ ] Role-based access works
- [ ] File upload works (MediaLibrary)
- [ ] Production Dockerfile builds
- [ ] CHANGELOG.md updated
- [ ] README.md updated

---

## 10. ROLLBACK PLAN

```bash
# If anything fails:
git checkout main
git branch -D update/major-deps-2026-09

# Restore backups:
cp composer.json.backup composer.json
cp package.json.backup package.json
composer install
npm install
```

---

*Update Plan v1.0 — 20 Sept 2026*
*Untuk review & approval sebelum execute.*