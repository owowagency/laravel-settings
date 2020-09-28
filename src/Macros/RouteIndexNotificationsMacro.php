<?php

namespace OwowAgency\LaravelNotifications\Macros;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use OwowAgency\LaravelNotifications\Models\Contracts\Notifiable;
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
                    if (! in_array(Notifiable::class, class_implements($notifiableClass))) {
                        throw new \Exception('Notifiable class must implement the Notifiable interface.');
                    }

                    $binding = Str::singular($prefix) ?: 'notifiable';

                    Route::get("$prefix/{{$binding}}/notifications", [
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
