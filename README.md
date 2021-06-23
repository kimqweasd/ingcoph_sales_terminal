# INGCOPH : POS Sales Terminal

## Installation

Install the dependencies and devDependencies and start the server.

```sh
composer install
composer dumpautoload
php artisan optimize:clear
php artisan migrate

npm install
npm run dev
```

```sh
php artisan serve --host 127.0.0.1 --port 80
or
php artisan serve
```