<?php

namespace OwowAgency\LaravelNotifications\Macros;

use Illuminate\Support\Facades\Route;
use OwowAgency\LaravelNotifications\Controllers\NotificationController;

class RouteIndexNotificationsMacro
{
    /**
     * Register the macro.
     *
     * @return void
     */
    public static function register(): void
    {
        Route::macro(
            'indexNotifications',
            function (string $prefix, string $notifiableClass = null) {
                if ($notifiableClass) {
                    Route::get("$prefix/{notifiable}/notifications", [
                        'uses' => NotificationController::class . '@indexForNotifiable',
                        'model' => $notifiableClass,
                    ]);
                } else {
                    Route::get("$prefix", [
                        'uses' => NotificationController::class . '@index',
                    ]);
                }
            },
        );
    }
}
