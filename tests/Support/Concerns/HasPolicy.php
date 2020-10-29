<?php

namespace OwowAgency\LaravelSettings\Tests\Support\Concerns;

use Illuminate\Support\Facades\Gate;
use OwowAgency\LaravelSettings\Tests\Support\Models\User;

trait HasPolicy
{
    /**
     * Mocks a policy. This is needed because the policy is not created in the
     * package, but in the project of the user itself. There we'll probably find
     * a UserPolicy.
     *
     * @param  bool  $allow
     * @return void
     */
    protected function mockPolicy(bool $allow): void
    {
        $abilities = ['viewSettings', 'updateSettings'];

        foreach ($abilities as $ability) {
            Gate::define(
                $ability,
                function (User $user, $target) use ($allow) {
                    return $allow;
                },
            );
        }
    }
}