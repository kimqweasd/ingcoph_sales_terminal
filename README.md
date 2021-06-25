# INGCOPH : POS Sales Terminal

## Installation

Install the dependencies and devDependencies and start the server.

```sh
composer install
composer dumpautoload
php artisan optimize:clear

npm install
```

First, serve the storehub, then the sales terminal, each @ different host or port
```sh
php artisan serve --host 127.0.0.1 --port 80
or
php artisan serve
```

After serving the storehub, put the host with the port on resources/js/api/index storeHub property
```sh
storeHub : new StoreHub({
    name : 'storeHub',
    address : {
        token : 'http://127.0.0.1:8000/oauth/token',
        library : 'http://127.0.0.1:8000/api/v1',
    },
})
```

then run
```sh
npm run dev
```
