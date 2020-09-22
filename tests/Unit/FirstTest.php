<?php

namespace OwowAgency\LaravelNotifications\Tests\Unit;

use Queue;
use Illuminate\Notifications\AnonymousNotifiable;
use OwowAgency\LaravelNotifications\Tests\TestCase;
use Illuminate\Notifications\SendQueuedNotifications;
use OwowAgency\LaravelNotifications\Tests\Support\SomeNotification;

class FirstTest extends TestCase
{
    /** @test */
    public function job_gets_queued(): void
    {
        Queue::fake();

        $notifiable = new AnonymousNotifiable;

        $notifiable->notify(new SomeNotification);

        Queue::assertPushed(function (SendQueuedNotifications $job) {
            return $job->notification instanceof SomeNotification;
        });
    }
}
