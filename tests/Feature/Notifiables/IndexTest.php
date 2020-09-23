<?php

namespace OwowAgency\LaravelNotifications\Tests\Feature\Notifiables;

use Illuminate\Support\Facades\Gate;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Route;
use OwowAgency\LaravelNotifications\Tests\TestCase;
use OwowAgency\LaravelNotifications\Tests\Support\Models\User;
use OwowAgency\LaravelNotifications\Tests\Support\Models\Notifiable;
use OwowAgency\LaravelNotifications\Tests\Support\Notifications\Notification;

class IndexTest extends TestCase
{
    /** @test */
    public function user_can_index_notifiable_notifications_if_allowed(): void
    {
        [$user, $notifiable] = $this->prepare();

        // Allow user to index notifiable's notifications.
        Gate::define('viewNotificationsOf', function ($user, $notifiable) {
            return true;
        });

        $response = $this->makeRequest($user, $notifiable);

        $this->assertResponse($response);
    }

    /** @test */
    public function user_cant_index_notifiable_notifications_if_disallowed(): void
    {
        [$user, $notifiable] = $this->prepare();

        // Disallow user to index notifiable's notifications.
        Gate::define('viewNotificationsOf', function ($user, $notifiable) {
            return false;
        });

        $response = $this->makeRequest($user, $notifiable);

        $this->assertResponse($response, 403);
    }

    /** @test */
    public function user_cant_index_notifiable_notifications_if_no_policy(): void
    {
        [$user, $notifiable] = $this->prepare();

        // Do not define policy.

        $response = $this->makeRequest($user, $notifiable);

        $this->assertResponse($response, 403);
    }

    /** @test */
    public function user_can_index_own_notifications(): void
    {
        [$user, $notifiable] = $this->prepare();

        // Allow user to index own notifications.
        Gate::define('viewNotificationsOf', function ($user, $target) {
            return $user->is($target);
        });

        // User should be able to index own notifications.
        $response1 = $this->makeRequest($user, $user);
        $this->assertResponse($response1);

        // User should not be able to index notifiable's notifications.
        $response2 = $this->makeRequest($user, $notifiable);
        $this->assertResponse($response2, 403);
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
        Route::paginateNotifications('notifiables', Notifiable::class);
        Route::paginateNotifications('users', User::class);
        
        $user = User::create();

        $notifiable = Notifiable::create();

        // Create notifications for user and notifiable.
        for ($i = 1; $i <= 3; $i++) {
            $user->notify(new Notification("Hello user! #$i"));

            $notifiable->notify(new Notification("Hello notifiable! #$i"));
        }

        return [$user, $notifiable];
    }

    /**
     * Makes a request.
     *
     * @param  \OwowAgency\LaravelNotifications\Tests\Support\Models\User  $user
     * @param  \Illuminate\Notifications\Notifiable  $notifiable
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function makeRequest(User $user, $notifiable): TestResponse
    {
        $prefix = $notifiable instanceof User ? 'users' : 'notifiables';
        
        return $this
            ->actingAs($user)
            ->json('GET', "$prefix/$notifiable->id/notifications");
    }

    /**
     * Asserts a response.
     *
     * @param  \Illuminate\Foundation\Testing\TestResponse  $response
     * @param  int  $status
     * @return void
     */
    private function assertResponse(TestResponse $response, int $status = 200): void
    {
        $response->assertStatus($status);

        if ($status !== 200) {
            return;
        }

        $this->assertJsonStructureSnapshot($response);
    }
}
