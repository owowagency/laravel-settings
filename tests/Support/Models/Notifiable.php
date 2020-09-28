<?php

namespace OwowAgency\LaravelNotifications\Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable as NotifiableTrait;
use OwowAgency\LaravelNotifications\Models\Contracts\Notifiable as NotifiableInterface;

class Notifiable extends Model implements NotifiableInterface
{
    use NotifiableTrait;
}
