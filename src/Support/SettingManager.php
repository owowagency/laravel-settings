<?php

namespace OwowAgency\LaravelSettings\Support;

use Illuminate\Support\Collection;
use OwowAgency\LaravelSettings\Models\Setting;
use OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface;

class SettingManager
{
    /**
     * Retrieves the settings for the specified model.
     * 
     * @param  \OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface $model
     * @return \Illuminate\Support\Collection
     */
    public static function getForModel(HasSettingsInterface $model): Collection
    {
        $settings = $model->settings()->get();

        static::fillMissing($settings);

        return $settings;
    }

    /**
     * Fills the missing settings.
     * 
     * @param  \Illuminate\Support\Collection  $settings
     * @return \Illuminate\Support\Collection
     */
    public static function fillMissing(Collection $settings): Collection
    {
        $configured = static::getConfigured();

        return $filled;
    }

    /**
     * Retrieves the configured settings.
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function getConfigured(): Collection
    {
        return collect(config('laravel-settings.settings', []))
            ->map(function ($attributes, $key) {
                return new Setting($attributes + ['key' => $key]);
            });
    }
}
