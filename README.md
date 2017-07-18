# laravel-statistics

## Installation
```shell
composer require newflit/statistics-laravel
```

## Register `ServiceProvider`
  ```php
  Newflit\Statistics\ServiceProvider::class,
  ```

## Add `Statistics` middleware into 'web' group. (app\Http\Kernel.php)
  ```php
  \Newflit\Statistics\Middleware\Statistics::class,
  ```

### Before Use, step 1: To create some database tables
```shell
php artisan migrate
```

### Before Use, step 2: To publish the configuration file
```shell
php artisan vendor:publish
```

## License

MIT
