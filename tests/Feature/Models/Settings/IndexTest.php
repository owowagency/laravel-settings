<?php

namespace OwowAgency\LaravelSettings\Tests\Feature\Models\Settings;

use Illuminate\Support\Facades\Gate;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Route;
use OwowAgency\LaravelSettings\Tests\TestCase;
use OwowAgency\LaravelSettings\Tests\Support\Models\User;
use OwowAgency\LaravelSettings\Models\Contracts\IHasSettings;

class IndexTest extends TestCase
{
    /** @test */
    public function user_can_index_own_settings_if_allowed(): void
    {
        [$user] = $this->prepare();

        // Allow user to index own settings.
        Gate::define('viewSettingsOf', function (User $user, $target) {
            // Only return true if the `authorize` method is called with the correct
            // User instance.
            return $target->is($user);
        });

        $response = $this->makeRequest($user, $user);
        $this->assertResponse($response);

        // Other shouldn't be allowed to index user's  settings.
        $other = User::create();
        $response = $this->makeRequest($other, $user);
        $this->assertResponse($response, 403);
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
        Route::indexSettings('users', User::class);

        $user = User::create();

        return [$user];
    }

    /**
     * Makes a request.
     *
     * @param  \OwowAgency\LaravelSettings\Tests\Support\Models\User  $user
     * @param  \Illuminate\Notifications\Notifiable  $notifiable
     * @param  string|null  $prefix
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function makeRequest(User $user, IHasSettings $model, string $prefix = null): TestResponse
    {
        if (is_null($prefix)) {
            $prefix = $model instanceof User ? 'users' : 'models';
        }
        
        return $this
            ->actingAs($user)
            ->json('GET', "$prefix/$model->id/settings");
    }
}
