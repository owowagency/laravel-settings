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

        // Guest should not be able to index notifications. This test should come first because
        // `actingAs` can not be reset to use guest.
        $response1 = $this->makeRequest(null);
        $this->assertResponse($response1, 403);

        // User should be able to index all notifications.
        $response2 = $this->makeRequest($user);
        $this->assertResponse($response2);
    }

    /** @test */
    public function user_can_index_all_notifications_custom_route(): void
    {
        // Instruct package to use custom routes.
        Route::paginateNotifications('');
        Route::paginateNotifications('notifs');

        [$user] = $this->prepare();

        Gate::define('viewAny', function (User $user, string $modelClass) {
            return $modelClass === DatabaseNotification::class;
        });

        // User should be able to index notifications from 'GET: /'.
        $response1 = $this->makeRequest($user, '');
        $this->assertResponse($response1);

        // User should be able to index notifications from 'GET: /notifs'.
        $response2 = $this->makeRequest($user, 'notifs');
        $this->assertResponse($response2);
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
     * @param  string  $route
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function makeRequest(?User $user, string $route = 'notifications'): TestResponse
    {
        if ($user) {
            $this->actingAs($user);
        }

        return $this->json('GET', $route);
    }
}
