<?php

namespace OwowAgency\LaravelNotifications\Tests\Support\Models;

use Illuminate\Foundation\Auth\User as BaseUser;
use Illuminate\Notifications\Notifiable as NotifiableTrait;

class User extends BaseUser
{
    use NotifiableTrait;
}
