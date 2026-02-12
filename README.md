To run: 
  npm run dev
  php artisan serve

What I would do differently:
I would update my php version on my computer. I only have version 8.0.0. This prevented me from running a newer version of laravel that would have allowed me to run a better retry + exponential backoff. I was only able to run a retry without an exponential retry, but luckily it still worked. 