<?php

namespace OwowAgency\LaravelNotifications\Tests\Feature\Notifiables\Notifications;

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
    public function notifiable_can_index_own_notifications(): void
    {
        [$user, $notifiable] = $this->prepare();

        Gate::define('viewNotificationsOf', function ($user, $target) {
            return true;
        });

        $response = $this->makeRequest($user, $notifiable);

        $this->assertResponse($response);
    }

    /** @test */
    public function notifiable_cant_index_others_notifications(): void
    {
        [$user, $notifiable] = $this->prepare();

        $response = $this->makeRequest($user, $notifiable);

        $this->assertResponse($response, 403);
    }

    /**
     * Prepares for tests.
     *
     * @return array
     */
    private function prepare(): array
    {
        // Prepare the API endpoint (route).
        Route::paginateNotifications('notifiables', Notifiable::class);
        
        $user = User::create();

        $notifiable = Notifiable::create();

        for ($i = 1; $i <= 3; $i++) { 
            $notifiable->notify(new Notification("Hello $i"));
        }

        return [$user, $notifiable];
    }

    /**
     * Makes a request.
     *
     * @param  \OwowAgency\LaravelNotifications\Tests\Support\Models\User  $user
     * @param  \OwowAgency\LaravelNotifications\Tests\Support\Models\Notifiable  $notifiable
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function makeRequest(User $user, Notifiable $notifiable): TestResponse
    {
        return $this
            ->actingAs($user)
            ->json('GET', "notifiables/$notifiable->id/notifications");
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
