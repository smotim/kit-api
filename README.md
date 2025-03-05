## KIT API Service

Copy the code and deploy on your machine. Double check for KIT env.
<pre>
cp .env.example .env
</pre>
You can generate api token here https://cabinet-new.tk-kit.com/developers/get-token

Once deployed, run:
<pre>
composer install
php artisan migrate
php artisan sync:all
</pre>

This will seed and sync all the data required to make KIT price calculations.
