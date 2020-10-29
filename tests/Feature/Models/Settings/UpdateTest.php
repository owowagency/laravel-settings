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

class UpdateTest extends TestCase
{
    use HasPolicy, HasSettings;

    /** @test */
    public function user_can_update_existing_settings(): void
    {
        [$user, $setting] = $this->prepare();

        // Allow the user to make the request.
        $this->mockPolicy(true);

        $data = [
            'key' => 'lang',
            'value' => 'en',
        ];

        $response = $this->makeRequest($user, $user, [
            'settings' => [$data],
        ]);

        $this->assertResponse($response);
        $this->assertDatabase($user, $data + ['id' => $setting->id]);
    }

    /** @test */
    public function user_can_create_new_settings(): void
    {
        [$user] = $this->prepare();

        // Allow the user to make the request.
        $this->mockPolicy(true);

        $data = [
            'key' => 'delete_account',
            'value' => 100,
        ];

        $response = $this->makeRequest($user, $user, [
            'settings' => [$data],
        ]);

        $this->assertResponse($response);
        $this->assertDatabase($user, $data);
    }

    /** @test */
    public function user_must_provide_valid_types(): void
    {
        [$user] = $this->prepare();

        // Allow the user to make the request.
        $this->mockPolicy(true);

        // The configuration value for lang has a type of string. So a boolean
        // should not be accepted by the request.
        $data = [
            'key' => 'lang',
            'value' => true,
        ];

        $response = $this->makeRequest($user, $user, [
            'settings' => [$data],
        ]);

        $this->assertResponse($response, 422);
        $this->assertDatabase($user, ['value' => 'nl'] + $data);
    }

    /** @test */
    public function user_can_update_null_to_nullable_config(): void
    {
        [$user] = $this->prepare();

        config(['laravel-settings.settings.lang.nullable' => true]);

        // Allow the user to make the request.
        $this->mockPolicy(true);

        $data = [
            'key' => 'lang',
            'value' => null,
        ];

        $response = $this->makeRequest($user, $user, [
            'settings' => [$data],
        ]);

        $this->assertResponse($response);
        $this->assertDatabase($user, $data);
    }

    /** @test */
    public function user_cannot_send_null(): void
    {
        [$user] = $this->prepare();

        // Allow the user to make the request.
        $this->mockPolicy(true);

        $data = [
            'key' => 'lang',
            'value' => null,
        ];

        $response = $this->makeRequest($user, $user, [
            'settings' => [$data],
        ]);

        $this->assertResponse($response, 422);
    }

    /** @test */
    public function user_cannot_update_settings(): void
    {
        [$user] = $this->prepare();

        // Do not allow the user to make the request.
        $this->mockPolicy(false);

        $response = $this->makeRequest($user, $user, ['settings' => [[
            'key' => 'lang',
            'value' => 'en',
        ]]]);

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
            'key' => 'lang',
            'value' => 'nl',
        ]);

        return [$setting->model, $setting];
    }

    /**
     * Makes a request.
     *
     * @param  \OwowAgency\LaravelSettings\Tests\Support\Models\User  $user
     * @param  \OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface  $hasSettings
     * @param  array  $data
     * @return \Illuminate\Testing\TestReponse
     */
    private function makeRequest(User $user, HasSettingsInterface $hasSettings, array $data): TestResponse
    {
        return $this->actingAs($user)
            ->json('PATCH', "/users/$hasSettings->id/settings", $data);
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

    /**
     * Assert that the database contains certain values.
     *
     * @param  \OwowAgency\LaravelSettings\Tests\Support\Models\User  $user
     * @param  array  $data
     * @return void
     */
    private function assertDatabase(User $user, array $data): void
    {
        $this->assertDatabaseHas(
            config('laravel-settings.table_name'),
            $data + [
                'model_type' => $user->getMorphClass(),
                'model_id' => $user->id,
            ],
        );
    }
}
