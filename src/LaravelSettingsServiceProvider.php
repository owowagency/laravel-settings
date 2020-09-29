<?php

namespace OwowAgency\LaravelSettings;

use DirectoryIterator;
use Illuminate\Support\ServiceProvider;
use OwowAgency\LaravelSettings\Macros\RouteIndexSettingsMacro;

class LaravelSettingsServiceProvider extends ServiceProvider
{
    /**
     * The package name.
     *
     * @var string
     */
    private $name = 'settings';
    
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
        $this->mergeConfigFrom(__DIR__."/../config/$this->name.php", $this->name);
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
        ], [$this->name, "$this->name.config", 'config']);

        // Database migrations.
        $this->registerPublishableMigrations();

        // HTTP resources.
        $this->publishes([
            __DIR__.'/Resources' => app_path('Http/Resources'),
        ], [$this->name, "$this->name.http_resources"]);
    }

    /**
     * Register database migrations to be published by the publish command.
     *
     * @return void
     */
    protected function registerPublishableMigrations(): void
    {
        $migrations = new DirectoryIterator(__DIR__.'/../database/migrations');

        foreach ($migrations as $migration) {
            if (! $migration->isDot()) {
                // Get the migration's filename without the timestamp prefix.
                $migrationName = explode('_', $migration->getFilename(), 5)[4];

                $timestamp = date('Y_m_d_His');

                $paths[$migration->getRealPath()] = database_path("migrations/{$timestamp}_{$migrationName}");
            }
        }

        $this->publishes($paths, [$this->name, "$this->name.migrations", 'migrations']);
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
