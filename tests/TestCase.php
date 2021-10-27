<?php

namespace OwowAgency\LaravelSettings\Tests;

use OwowAgency\Snapshots\MatchesSnapshots;
use OwowAgency\LaravelTestResponse\TestResponse;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;
use OwowAgency\LaravelSettings\LaravelSettingsServiceProvider;
use OwowAgency\LaravelSettings\Tests\Support\Concerns\HasSettings;
use OwowAgency\LaravelSettings\Tests\Support\Concerns\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithTime, MatchesSnapshots, RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Run the package's migrations.
        $this->artisan('migrate:fresh')->run();

        // Run the tests' migrations.
        $this->loadMigrationsFrom(__DIR__.'/Support/database/migrations');
    }

    /**
     * Boot the testing helper traits.
     *
     * @return array
     */
    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        if (isset($uses[HasSettings::class])) {
            $this->setupSettings();
        }

        return $uses;
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelSettingsServiceProvider::class,
        ];
    }

    /**
     * Create the test response instance from the given response.
     *
     * @param  \Illuminate\Http\Response  $response
     * @return \Illuminate\Testing\TestResponse
     */
    protected function createTestResponse($response)
    {
        return TestResponse::fromBaseResponse($response);
    }
}
