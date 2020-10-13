<?php

namespace OwowAgency\LaravelSettings;

use Illuminate\Support\ServiceProvider;
use OwowAgency\LaravelSettings\Macros\RouteIndexSettingsMacro;

class LaravelSettingsServiceProvider extends ServiceProvider
{
    /**
     * The name of the package.
     *
     * @var string
     */
    private $name = 'laravel-settings';
    
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

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
        $this->mergeConfigFrom(__DIR__ . "/../config/$this->name.php", $this->name);
    }

    /**
     * Register files to be published by the publish command.
     *
     * @return void
     */
    protected function registerPublishableFiles(): void
    {
        $this->publishes(
            [
                __DIR__ . "/../config/$this->name.php" => config_path("$this->name.php"),
            ],
            [$this->name, "$this->name.config", 'config'],
        );

        $this->publishes(
            [
                __DIR__ . '/../database/migrations'
            ],
            [$this->name, "$this->name.migrations", 'migrations'],
        );

        $this->publishes(
            [
                __DIR__ . '/Resources' => app_path('Http/Resources'),
            ],
            [$this->name, "$this->name.http_resources"],
        );
    }

    /**
     * Register macros.
     *
     * @return void
     */
    protected function registerMacros(): void
    {
        RouteIndexSettingsMacro::register();
    }
}
