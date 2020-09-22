<?php

namespace OwowAgency\LaravelResources;

use Illuminate\Support\ServiceProvider;

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
}
