<?php

namespace OwowAgency\LaravelNotifications\Macros;

use Illuminate\Support\Facades\Route;
use OwowAgency\LaravelNotifications\Controllers\NotificationController;

class RouteCountNotificationsMacro
{
    /**
     * Register the macro.
     *
     * @return void
     */
    public static function register(): void
    {
        Route::macro(
            'countNotifications',
            function (string $prefix, string $notifiableClass) {
                Route::get("$prefix/{notifiable}/notifications/count", [
                    'uses' => NotificationController::class . '@countForNotifiable',
                    'model' => $notifiableClass,
                ]);
            },
        );
    }
}
