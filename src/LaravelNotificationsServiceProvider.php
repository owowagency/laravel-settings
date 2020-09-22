<?php

namespace OwowAgency\LaravelNotifications;

use Illuminate\Support\ServiceProvider;
use OwowAgency\LaravelNotifications\Macros\RoutePaginateNotificationsMacro;

class LaravelNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->registerPublishPaths();

        $this->registerMacros();
    }

    /**
     * Register paths to be published by the publish command.
     *
     * @return void
     */
    protected function registerPublishPaths(): void
    {
        $this->publishes([
            __DIR__ . '/../config/notifications.php' => config_path('notifications.php'),
        ], 'notifications');
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
