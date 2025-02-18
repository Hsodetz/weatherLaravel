
start:
	php artisan migrate:rollback --force
	php artisan migrate --force
	php artisan db:seed --force
	php artisan reverb:start &
	php artisan queue:work redis &
	php artisan serve
