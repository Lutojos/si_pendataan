#ganti compose file sesuai environment
compose-file = docker-compose-dev.yml
run-manual:
	php -S localhost:8000 -t ./public
docker-start:
	docker-compose -f $(compose-file) start
docker-build:
	docker-compose -f $(compose-file) up --build --remove-orphans --force-recreate -d
docker-build-watch:
	docker-compose -f $(compose-file) up --build --remove-orphans --force-recreate
docker-stop:
	docker-compose -f $(compose-file) stop
docker-run-bash:
	docker-compose -f $(compose-file) exec app bash
docker-down:
	docker-compose -f $(compose-file) down
dump-autoload:
	docker-compose -f $(compose-file) exec app composer dump-autoload
optimize:
	docker-compose -f $(compose-file) exec app php artisan optimize:clear
docker-migrate:
	docker-compose -f $(compose-file) exec app php artisan migrate
octane-reload:
	docker-compose -f $(compose-file) exec app php artisan octane:reload
composer-install:
	docker-compose -f $(compose-file) exec app composer install
docker-migrate-refresh:
	docker-compose -f $(compose-file) exec app php artisan migrate:refresh --seed

