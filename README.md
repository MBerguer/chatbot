## About this project

This is a simple Laravel web application that uses a chat bot interface to perform different transactions in a imaginary (and local) account.

## How to run

Five simple steps:

-   Clone the project
-   Create your own environment `copy .env.example .env` and replace the AMDOREN key to your own.
-   On the root directory of the priyect run `docker-compose up -d --build`
-   To get the DB up and ready with the currencies list run `docker-compose exec myapp php artisan migrate:refresh --seed`
-   open your browser and access `0.0.0.0:3000`
-   enjoy

## How to test

Runnning this command:

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
