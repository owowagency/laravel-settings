<?php

namespace OwowAgency\LaravelNotifications;

use Illuminate\Support\ServiceProvider;
use OwowAgency\LaravelNotifications\Macros\RoutePaginateNotificationsMacro;

class LaravelNotificationsServiceProvider extends ServiceProvider
{
    /**
     * The package name.
     *
     * @var string
     */
    private $name = 'notifications';
    
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->registerPublishableFiles();
        $this->registerMacros();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__."/../config/$this->name.php", "$this->name");
    }

    /**
     * Register files to be published by the publish command.
     *
     * @return void
     */
    protected function registerPublishableFiles(): void
    {
        $this->publishes([
            __DIR__."/../config/$this->name.php" => config_path("$this->name.php"),
        ], 'config');
    }

    /**
     * Register macros.
     *
     * @return void
     */
    protected function registerMacros(): void
    {
        RoutePaginateNotificationsMacro::register();
    }
}
