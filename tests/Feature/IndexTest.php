<?php

namespace OwowAgency\LaravelNotifications\Tests\Feature;

use Illuminate\Support\Facades\Gate;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Notifications\DatabaseNotification;
use OwowAgency\LaravelNotifications\Tests\TestCase;
use OwowAgency\LaravelNotifications\Tests\Support\Models\User;

class IndexTest extends TestCase
{
    /** @test */
    public function user_can_index_all_notifications_if_allowed(): void
    {
        [$user] = $this->prepare();

        // Allow user to index all notifications.
        Gate::define('viewAny', function (User $user, string $modelClass) {
            // Only return true if the `authorize` method is called with DatabaseNotification
            // class name.
            return $modelClass === DatabaseNotification::class;
        });

        $response = $this->makeRequest($user);

        $this->assertResponse($response);
    }

    /**
     * Helper Methods
     * ========================================================================
     */

    /**
     * Prepares for tests.
     *
     * @return array
     */
    private function prepare(): array
    {
        // Prepare the API endpoint (route).
        Route::paginateNotifications('notifications');
        
        return $this->prepareNotifications();
    }

    /**
     * Makes a request.
     *
     * @param  \OwowAgency\LaravelNotifications\Tests\Support\Models\User  $user
     * @param  string  $endpoint
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function makeRequest(User $user, string $endpoint = 'notifications'): TestResponse
    {
        return $this
            ->actingAs($user)
            ->json('GET', $endpoint);
    }
}
