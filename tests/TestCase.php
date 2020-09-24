<?php

namespace OwowAgency\LaravelNotifications\Tests;

use OwowAgency\Snapshots\MatchesSnapshots;
use OwowAgency\LaravelTestResponse\TestResponse;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;
use OwowAgency\LaravelNotifications\Tests\Support\Models\User;
use OwowAgency\LaravelNotifications\Tests\Support\Models\Notifiable;
use OwowAgency\LaravelNotifications\LaravelNotificationsServiceProvider;
use OwowAgency\LaravelNotifications\Tests\Support\Notifications\Notification;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithTime, MatchesSnapshots;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Run the service providers' migrations.
        $this->artisan('migrate:fresh');

        // Run the tests' migrations.
        $this->loadMigrationsFrom(__DIR__ . '/Support/database/migrations');
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
            LaravelNotificationsServiceProvider::class,
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
     * Prepare a user, a notifiable, and some notifications.
     *
     * @return array
     */
    protected function prepareNotifications(): array
    {
        $user = User::create();

        $notifiable = Notifiable::create();

        // Create notifications for user and notifiable.
        for ($i = 1; $i <= 5; $i++) {
            $user->notify(new Notification("Hello user! #$i"));

            $notifiable->notify(new Notification("Hello notifiable! #$i"));

            $this->travel(1)->minutes();
        }

        // Mark some notifications as read.
        $user->unreadNotifications()->take(2)->update(['read_at' => now()]);
        $notifiable->unreadNotifications()->take(2)->update(['read_at' => now()]);

        return [$user, $notifiable];
    }

    /**
     * Asserts a response.
     *
     * @param  \Illuminate\Foundation\Testing\TestResponse  $response
     * @param  int  $status
     * @return void
     */
    protected function assertResponse(TestResponse $response, int $status = 200): void
    {
        $response->assertStatus($status);

        if ($status !== 200) {
            return;
        }

        $this->assertJsonStructureSnapshot($response);
    }
}
