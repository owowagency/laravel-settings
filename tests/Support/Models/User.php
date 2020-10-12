<?php

namespace OwowAgency\LaravelSettings\Tests\Support\Models;

use Illuminate\Foundation\Auth\User as BaseUser;
use OwowAgency\LaravelSettings\Models\Concerns\HasSettings;
use OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface;

class User extends BaseUser implements HasSettingsInterface
{
    use HasSettings;
}
