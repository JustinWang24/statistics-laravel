<?php
/**
 * Created by PhpStorm.
 * User: justinwang
 * Date: 18/7/17
 * Time: 9:23 AM
 */

namespace Newflit\Statistics;

use Illuminate\Support\ServiceProvider as LaravelProvider;
use Newflit\Statistics\ViewLoader as NewflitStatisticsViewLoader;

class ServiceProvider extends LaravelProvider
{
    /**
     * Register the provider
     *
     * @return void
     */
    public function register(){
        $this->app->singleton('viewLoader', function ($app) {
                $viewLoader = new NewflitStatisticsViewLoader($app);
                return $viewLoader;
            }
        );
    }

    /**
     * Boot the provider
     *
     * @return void
     */
    public function boot(){
        // Publish configuration files
        $this->publishes([
            __DIR__.'/config.php' => config_path('statistics.php')
        ]);

        // Run migration
        $this->loadMigrationsFrom(__DIR__.'/migrations');

        // Publish routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Publish Views
        $this->loadViewsFrom(__DIR__.'/views/nf_statistics', 'statistics-laravel');
        $this->publishes([
            __DIR__.'/views/nf_statistics' => resource_path('views/vendor/statistics-laravel'),
        ]);
    }
}