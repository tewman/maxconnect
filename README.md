# MaxConnect – Campaign metrics dashboard

Laravel + Vue 3 + Inertia.js + Tailwind app that fetches digital marketing campaign data from an external API, aggregates six performance metrics, and displays the totals on a dashboard.

## Prerequisites

- **PHP** 8.0+ (8.0.2+ recommended for Laravel 9)
- **Composer**
- **Node.js** 18+ and **npm**

## Setup

**.env is included** in this repo for testing, so no environment setup is required.

1. **Clone the repo**
   ```bash
   git clone https://github.com/YOUR_USERNAME/maxconnect.git
   cd maxconnect
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```
   If your PHP is below 8.0.2 and Composer blocks install, use:
   ```bash
   composer install --ignore-platform-reqs
   ```

3. **Install frontend dependencies and build**
   ```bash
   npm install
   npm run build
   ```

## Run the app

1. **Start the Laravel server**
   ```bash
   php artisan serve
   ```
   Default URL: **http://127.0.0.1:8000**

2. **Optional – dev with hot reload**  
   In a second terminal, run:
   ```bash
   npm run dev
   ```
   Keep both `php artisan serve` and `npm run dev` running and open http://127.0.0.1:8000

## What you’ll see

- One page at `/` that shows **Campaign metrics**: aggregated totals for **budget**, **impressions**, **clicks**, **conversions**, **users**, and **sessions** from the API.
- If the API is unreachable or misconfigured, the page shows an error message instead of the metrics.

## Tech stack

- **Backend:** Laravel 9, Inertia (server)
- **Frontend:** Vue 3, Inertia (client), Vite, Tailwind CSS


## What I would do differently:<br/>
<div>
<ol>
<li>I would add .env to the .gitignore. But I allowed it this time so you wouldn't have to create it.</li>
<li>I would update my php version on my computer. I only have version 8.0.0. This prevented me from running a newer version of laravel that would have allowed me to run a better retry + exponential backoff. I was only able to run a retry without an exponential retry.</li>
</ol>
</div>
