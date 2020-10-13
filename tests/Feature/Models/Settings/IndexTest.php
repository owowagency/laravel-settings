<?php

namespace OwowAgency\LaravelSettings\Tests\Feature\Models\Settings;

use Illuminate\Support\Facades\Gate;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Route;
use OwowAgency\LaravelSettings\Models\Setting;
use OwowAgency\LaravelSettings\Tests\TestCase;
use OwowAgency\LaravelSettings\Tests\Support\Models\User;
use OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface;

class IndexTest extends TestCase
{
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
        Route::indexSettings('users', User::class);

        $user = User::create();

        $settings = Setting::factory()->count(3)->create([
            'model_id' => $user->id,
            'model_type' => (new User)->getMorphClass(),
        ]);

        return [$user, $settings];
    }

    /**
     * Mocks a policy. This is needed because the policy is not created in the
     * package, but in the project of the user itself. There we'll probably find
     * a UserPolicy.
     * 
     * @param  bool  $allow
     * @return void
     */
    private function mockPolicy(bool $allow): void
    {
        Gate::define(
            'viewSettingsOf',
            function(User $user, $target) use ($allow) {
                return $allow;
            },
        );
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
