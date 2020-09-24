<?php

namespace OwowAgency\LaravelNotifications\Tests\Feature\Notifiables;

use Illuminate\Support\Facades\Gate;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Route;
use OwowAgency\LaravelNotifications\Tests\TestCase;
use OwowAgency\LaravelNotifications\Tests\Support\Models\User;
use OwowAgency\LaravelNotifications\Tests\Support\Models\Notifiable;

class CountTest extends TestCase
{
    /** @test */
    public function user_can_count_notifiable_notifications_if_allowed(): void
    {
        [$user, $notifiable] = $this->prepare();

        // Allow user to view notifiable's notifications count.
        Gate::define('viewNotificationsCountOf', function (User $user, $target) use ($notifiable) {
            // Only return true if the `authorize` method is called with the correct
            // Notifiable instance.
            return $target->is($notifiable);
        });

        $response = $this->makeRequest($user, $notifiable);

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
        // Prepare the API endpoints (routes).
        Route::countNotifications('notifiables', Notifiable::class);
        Route::countNotifications('users', User::class);
        
        return $this->prepareNotifications();
    }

    /**
     * Makes a request.
     *
     * @param  \OwowAgency\LaravelNotifications\Tests\Support\Models\User  $user
     * @param  \Illuminate\Notifications\Notifiable  $notifiable
     * @param  string  $prefix
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function makeRequest(User $user, $notifiable, string $prefix = null): TestResponse
    {
        if (is_null($prefix)) {
            $prefix = $notifiable instanceof User ? 'users' : 'notifiables';
        }
        
        return $this
            ->actingAs($user)
            ->json('GET', "$prefix/$notifiable->id/notifications/count");
    }
}
