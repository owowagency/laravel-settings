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
    public function user_can_count_own_notifications_if_allowed(): void
    {
        [$user, $notifiable] = $this->prepare();

        // Allow user to view own notifications count.
        Gate::define('viewNotificationsCountOf', function (User $user, $target) {
            // Only return true if the `authorize` method is called with the correct
            // User instance.
            return $target->is($user);
        });

        // User should be able to count own notifications.
        $response = $this->makeRequest($user, $user);
        $this->assertResponse($response);

        // User should not be able to count notifiable's notifications.
        $response = $this->makeRequest($user, $notifiable);
        $this->assertResponse($response, 403);
    }

    /** @test */
    public function user_can_count_own_notifications_none(): void
    {
        [$user] = $this->prepare();

        Gate::define('viewNotificationsCountOf', function (User $user, $target) {
            return $target->is($user);
        });

        // Delete the user's notifications.
        $user->notifications()->delete();

        // All counts should be 0.
        $response = $this->makeRequest($user, $user);
        $this->assertResponse($response);
    }

    /** @test */
    public function user_can_count_notifiable_notifications_custom_route(): void
    {
        // Instruct package to use custom routes.
        Route::countNotifications('', Notifiable::class);
        Route::countNotifications('players', Notifiable::class);
        Route::prefix('custom')->group(fn() => Route::countNotifications('', Notifiable::class));
        
        [$user, $notifiable] = $this->prepare();

        // Allow user to count notifiable's notifications.
        Gate::define('viewNotificationsCountOf', function (User $user, $target) use ($notifiable) {
            // Only return true if the `authorize` method is called with the correct
            // Notifiable instance.
            return $target->is($notifiable);
        });

        // User should be able to count notifiable's notifications from 'GET: /{id}/notifications/count'.
        $response = $this->makeRequest($user, $notifiable, '');
        $this->assertResponse($response);

        // User should be able to count notifiable's notifications from 'GET: /players/{id}/notifications/count'.
        $response = $this->makeRequest($user, $notifiable, 'players');
        $this->assertResponse($response);

        // User should be able to count notifiable's notifications from 'GET: /custom/{id}/notifications/count'.
        $response = $this->makeRequest($user, $notifiable, 'custom');
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

        // We want to strictly compare the counts with previous snapshot.
        $this->assertMatchesJsonSnapshot($response->getContent());
    }
}
