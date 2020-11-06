<?php

namespace OwowAgency\LaravelSettings\Tests\Feature\Models\Settings;

use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Route;
use OwowAgency\LaravelSettings\Models\Setting;
use OwowAgency\LaravelSettings\Tests\TestCase;
use OwowAgency\LaravelSettings\Tests\Support\Models\User;
use OwowAgency\LaravelSettings\Tests\Support\Concerns\HasPolicy;
use OwowAgency\LaravelSettings\Tests\Support\Concerns\HasSettings;
use OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface;

class IndexTest extends TestCase
{
    use HasPolicy, HasSettings;

    /** @test */
    public function user_can_index_settings(): void
    {
        [$user] = $this->prepare();

        // Allow the user to make the request.
        $this->mockPolicy(true);

        $response = $this->makeRequest($user, $user);

        $this->assertResponse($response);
    }

    /** @test */
    public function user_cannot_index_settings(): void
    {
        [$user] = $this->prepare();

        // Do not allow the user to make the request.
        $this->mockPolicy(false);

        $response = $this->makeRequest($user, $user);

        $this->assertResponse($response, 403);
    }

    /**
     * Prepares for tests.
     *
     * @return array
     */
    private function prepare(): array
    {
        Route::settings('users', User::class);

        $setting = Setting::factory()->create([
            'group' => 'app_settings',
            'key' => 'dark_mode',
            'value' => true,
        ]);

        return [$setting->model, $setting];
    }

    /**
     * Makes a request.
     *
     * @param  \OwowAgency\LaravelSettings\Tests\Support\Models\User  $user
     * @param  \OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface  $hasSettings
     * @return \Illuminate\Testing\TestReponse
     */
    private function makeRequest(User $user, HasSettingsInterface $hasSettings): TestResponse
    {
        return $this->actingAs($user)
            ->json('GET', "/users/$hasSettings->id/settings");
    }

    /**
     * Asserts a response.
     *
     * @param  \Illuminate\Testing\TestResponse  $response
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
