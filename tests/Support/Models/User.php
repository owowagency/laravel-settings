<?php

namespace OwowAgency\LaravelSettings\Tests\Support\Models;

use Illuminate\Foundation\Auth\User as BaseUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwowAgency\LaravelSettings\Models\Concerns\HasSettings;
use OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface;
use OwowAgency\LaravelSettings\Tests\Support\Database\Factories\UserFactory;

class User extends BaseUser implements HasSettingsInterface
{
    use HasFactory, HasSettings;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
