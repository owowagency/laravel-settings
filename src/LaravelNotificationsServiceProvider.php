<?php

namespace OwowAgency\LaravelNotifications;

use Illuminate\Support\ServiceProvider;
use OwowAgency\LaravelNotifications\Macros\RouteIndexNotificationsMacro;

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
        // Config file.
        $this->publishes([
            __DIR__."/../config/$this->name.php" => config_path("$this->name.php"),
        ], ["$this->name", "$this->name.config", 'config']);

        // Database migrations: create notifications table.
        $timestamp = date('Y_m_d_His');
        $this->publishes([
            __DIR__.'/../database/migrations/0000_00_00_000000_create_notifications_table.php'
                => database_path("migrations/{$timestamp}_create_notifications_table.php"),
        ], ["$this->name", "$this->name.migrations", 'migrations']);

        // HTTP resources.
        $this->publishes([
            __DIR__.'/Resources' => app_path('Http/Resources'),
        ], ["$this->name.http_resources"]);
    }

    /**
     * Register macros.
     *
     * @return void
     */
    protected function registerMacros(): void
    {
        RouteIndexNotificationsMacro::register();
    }
}
