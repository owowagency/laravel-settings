<?php

namespace OwowAgency\LaravelNotifications\Tests\Feature\Notifiables\Notifications;

use Illuminate\Testing\TestResponse;
use OwowAgency\LaravelNotifications\Tests\TestCase;
use OwowAgency\LaravelNotifications\Tests\Support\Models\Notifiable;

class IndexTest extends TestCase
{
    /** @test */
    public function notifiable_can_index_own_notifications(): void
    {
        [$notifiable] = $this->prepare();

        $response = $this->makeRequest($notifiable);

        $this->assertResponse($response);
    }

    /**
     * Prepares for tests.
     *
     * @return array
     */
    private function prepare(): array
    {
        $notifiable = Notifiable::create();

        return [$notifiable];
    }

    /**
     * Makes a request.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function makeRequest(Notifiable $notifiable): TestResponse
    {
        return $this->json('GET', "$notifiable->id/notifications");
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
