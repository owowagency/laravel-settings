<?php

namespace OwowAgency\LaravelNotifications\Tests\Support\Models;

use Illuminate\Foundation\Auth\User as BaseUser;
use Illuminate\Notifications\Notifiable as NotifiableTrait;
use OwowAgency\LaravelNotifications\Models\Contracts\Notifiable;

class User extends BaseUser implements Notifiable
{
    use NotifiableTrait;
}
