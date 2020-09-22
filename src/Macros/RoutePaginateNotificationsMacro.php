<?php

namespace OwowAgency\LaravelNotifications\Macros;

use Illuminate\Support\Facades\Route;
use OwowAgency\LaravelNotifications\Controllers\NotificationController;

class RoutePaginateNotificationsMacro
{
    /**
     * Register the macro.
     *
     * @return void
     */
    public static function register(): void
    {
        Route::macro(
            'paginateNotifications',
            function (string $uri, string $notifiableClass = null) {
                Route::get(
                    "$uri/{notifiable}/notifications",
                    [NotificationController::class, 'paginateForNotifiable'],
                );
            },
        );
    }
}
