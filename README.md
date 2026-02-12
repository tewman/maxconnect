To run: <br/>
  <div>npm run dev</div>
  <div>php artisan serve</div>
  <br/>
  <div>http://127.0.0.1:8000/</div>
<br/>
What I would do differently:<br/>
<div>
<ol>
<li>I would add .env to the .gitignore. But I allowed it this time so you wouldn't have to create it.</li>
<li>I would update my php version on my computer. I only have version 8.0.0. This prevented me from running a newer version of laravel that would have allowed me to run a better retry + exponential backoff. I was only able to run a retry without an exponential retry.</li>
</ol>
</div>