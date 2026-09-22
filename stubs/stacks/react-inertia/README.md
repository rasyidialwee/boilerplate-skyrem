# Skyrem Application

This project was bootstrapped with [`skyrem/boilerplate`](https://github.com/skyrem/boilerplate).

## Quick start (Sail)

```bash
cp .env.example .env   # or use .env.dev.example
cp compose-dev.yaml compose.yaml
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
./vendor/bin/sail artisan migrate --seed
```

See `README-PRODUCTION.md` for production Docker notes and `NOTIFICATIONS.md` for in-app notifications.
