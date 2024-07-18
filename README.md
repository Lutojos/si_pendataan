# SAMBINA GROUP.
SAMBINA GROUP CMS AND API

## FRAMEWORK
LARAVEL FRAMEWORK 9.0
PHP 8.1^

## HOW TO INSTALL MANNUAL
Clone from repository
Run composer install
Setup .env you can check updated parameters in .env.example
run php artisan jwt:secret for jwt secret key
php artisan serve

## HOW TO BUILD WITH DOCKER
Setup .env you can check updated parameters in .env.example
change compose-file value to your environtment type "make docker-build" or you can see command at makefile

## NOTES
if install in staging or production server change
APP_ENV to "production/staging"
FORCE_HTTPS to "true"
OCTANE_HTTPS to true
APP_DEBUG set to "false" in .env file
