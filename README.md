## About this project

This is a simple Laravel web application that uses a chatbot interface to perform different transactions in an imaginary (and local) account.

## How to run

Five simple steps:

-   Clone the project
-   Create your own environment file running `cp .env.example .env` and replace the AMDOREN key to your own.
-   On the root directory of the project run `docker-compose up -d --build`
-   Update composer `docker-compose exec myapp composer install`
-   Generate key `docker-compose exec myapp php artisan key:generate`
-   To get the DB up and ready with the currencies list run `docker-compose exec myapp php artisan migrate --seed`
-   open your browser and access `0.0.0.0:3000`
-   enjoy

## Possible troubleshooting commands in case of errors

-   If an error comes up about the Botman Service provider:: `docker-compose exec myapp composer global require "botman/installer"`
-   Update composer `docker-compose exec myapp composer update`
-   Clean laravel entirely
    `docker-compose exec myapp php artisan cache:clear && docker-compose exec myapp php artisan route:clear && docker-compose exec myapp php artisan view:clear && docker-compose exec myapp php artisan config:clear && docker-compose exec myapp php artisan config:cache`

## How to test

Running this command:

-   `docker-compose exec myapp ./vendor/bin/phpunit`

## Tools used

-   Docker
-   PHP\Laravel\Botman
-   Mariadb
-   AMDOREN API (for the currency exchange rate services, please add your own key to the .env before start)

In case you encounter any issues, run
`docker-compose exec myapp php artisan cache:clear && php artisan route:clear`

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
