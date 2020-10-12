<?php

namespace OwowAgency\LaravelSettings\Tests;

use OwowAgency\Snapshots\MatchesSnapshots;
use OwowAgency\LaravelTestResponse\TestResponse;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;
use OwowAgency\LaravelSettings\LaravelSettingsServiceProvider;

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

    /**
     * Optional Helper Methods
     * ========================================================================
     */

    /**
     * Asserts a response.
     *
     * @param  \Illuminate\Foundation\Testing\TestResponse  $response
     * @param  int  $status
     * @return void
     */
    // protected function assertResponse(TestResponse $response, int $status = 200): void
    // {
    //     $response->assertStatus($status);

    //     if (in_array($status, [204, 403])) return;

    //     $status === 422
    //         ? $this->assertMatchesJsonSnapshot($response->getContent())
    //         : $this->assertJsonStructureSnapshot($response);
    // }
}
