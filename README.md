# laravel-statistics

<p>To activate all features, please consider switching your application to a secure origin, such as HTTPS. </p>
<p>FYI, if still using insecure website, the location service is blocked.</p>
<p>This package is created for you to collect all your visitors behavior data and general a detailed report of how they
interact with your web page, form and links etc. You might customise this package as you want.</p>
<p>Notice: this project is still under development. Please don't use it on the production server until the formal version released!</p>

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

### Before Use, step 3: Please make sure import jQuery in the header in any page you want track.

## License

MIT
