<?php

namespace OwowAgency\LaravelSettings\Macros;

use Illuminate\Support\Facades\Route;
use OwowAgency\LaravelSettings\Controllers\SettingController;
use OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface;

class RouteIndexSettingsMacro
{
    /**
     * Register the macro.
     *
     * @return void
     */
    public static function register(): void
    {
        Route::macro(
            'indexSettings',
            function (string $prefix, string $modelClass, string $postfix = 'settings') {
                validate_interfaces_implemented($modelClass, HasSettingsInterface::class);

                $binding = strtolower(class_basename($modelClass)) ?: 'model';
                
                Route::get("$prefix/{{$binding}}/$postfix", [
                    'uses' => SettingController::class . '@indexForModel',
                    'model' => $modelClass,
                ]);
            },
        );
    }
}
