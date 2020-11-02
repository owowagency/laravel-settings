<?php

namespace OwowAgency\LaravelSettings\Macros;

use Illuminate\Support\Facades\Route;
use OwowAgency\LaravelSettings\Http\Controllers\SettingController;
use OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface;

class SettingsRouteMacro
{
    /**
     * Register the macro.
     *
     * @return void
     */
    public static function register(): void
    {
        Route::macro(
            'settings',
            function (string $prefix, string $modelClass, string $postfix = 'settings') {
                validate_interfaces_implemented($modelClass, HasSettingsInterface::class);

                $binding = strtolower(class_basename($modelClass)) ?: 'model';
                
                Route::get("$prefix/{{$binding}}/$postfix", [
                    'uses' => SettingController::class . '@indexForModel',
                    'model' => $modelClass,
                ]);

                Route::patch("$prefix/{{$binding}}/$postfix", [
                    'uses' => SettingController::class . '@update',
                    'model' => $modelClass,
                ]);
            },
        );
    }
}
