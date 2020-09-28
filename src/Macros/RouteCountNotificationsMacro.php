<?php

namespace OwowAgency\LaravelNotifications\Macros;

use Illuminate\Support\Facades\Route;
use OwowAgency\LaravelNotifications\Models\Contracts\Notifiable;
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
                if (! in_array(Notifiable::class, class_implements($notifiableClass))) {
                    throw new \Exception('Notifiable class must implement the Notifiable interface.');
                }

                Route::get("$prefix/{notifiable}/notifications/count", [
                    'uses' => NotificationController::class . '@countForNotifiable',
                    'model' => $notifiableClass,
                ]);
            },
        );
    }
}
