<?php
/**
 * Created by PhpStorm.
 * User: justinwang
 * Date: 18/7/17
 * Time: 9:23 AM
 */

namespace Newflit\Statistics;

use Illuminate\Support\ServiceProvider as LaravelProvider;

class ServiceProvider extends LaravelProvider
{
    /**
     * Register the provider
     *
     * @return void
     */
    public function register(){

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
    }
}