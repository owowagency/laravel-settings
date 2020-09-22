<?php

namespace OwowAgency\LaravelNotifications\Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable as NotifiableTrait;

class Notifiable extends Model
{
    use NotifiableTrait;
}
