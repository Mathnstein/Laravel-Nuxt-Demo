<div align="center">
  <br />
  <a href="https://laravel.com" target="_blank" style="display: inline-block; vertical-align: middle;">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
  </a>

  <p>+++++++++++++++++++++++++++++++++</p>

  <a href="https://nuxt.com" target="_blank" style="display: inline-block; vertical-align: middle;">
    <img src="public/nuxt-logo.svg" width="250" alt="Nuxt Logo">
  </a>
  <br /><br />
</div>

A simple todo app that demos off how a full stack application can be written in Nuxt / Laravel.

## Tech Stack

    Frontend: Nuxt, Tailwind CSS

    Backend: Laravel

    Infrastructure: Laravel Sail (Docker)

    Cache/Queue: Redis

    Database: MySQL

## Project Structure
```Plaintext
.
├── frontend/           # Nuxt Frontend
│   ├── api/            # Managed API Services & Interceptors
│   ├── pages/          # Auto-routed Vue components
│   └── app.vue         # Core layout with overflow & navigation logic
├── ./                  # Laravel Backend (Standard Structure> routes/, app/models/ etc...)
└── compose.yml         # Lab Orchestration
└── compose.prod.yml    # For using prebuild image
```

## Getting Started
1. Spin up the Lab
```Bash
./vendor/bin/sail up -d
```

2. Prepare the Frontend
```Bash
cd frontend
npm install
npx nuxi prepare
```

3. Run Migrations
```Bash
./vendor/bin/sail artisan migrate
```

## Production

To pull the most recent image from docker hub
```Bash
docker-compose -f compose.prod.yaml --env-file .env.prod up -d
```

This lab is open-sourced software licensed under the MIT license.
