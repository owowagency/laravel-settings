<?php

namespace OwowAgency\LaravelSettings\Support;

use Illuminate\Support\Collection;
use OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface;

class SettingManager
{
    /**
     * Retrieves the settings for the specified model.
     *
     * @param  \OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface  $model
     * @return \Illuminate\Support\Collection
     */
    public static function getForModel(HasSettingsInterface $model): Collection
    {
        $settings = $model->settings()->get();

        return static::mergeWithSettingsConfig($settings);
    }

    /**
     * Merge the settings collection with the settings config..
     *
     * @param  \Illuminate\Support\Collection  $settings
     * @return \Illuminate\Support\Collection
     */
    public static function mergeWithSettingsConfig(
        Collection $settings
    ): Collection {
        $callback = function ($configuration, $key) use ($settings) {
            $setting = $settings->firstWhere('key', $key);

            $configuration['key'] = $key;

            // The settings config default values will be used as fallback.
            $configuration['value'] = $setting === null
                ? $configuration['default']
                : $setting->value;

            return $configuration;
        };

        return static::getConfigured()->map($callback)->values();
    }

    /**
     * Convert the given value to the given type.
     *
     * @param  string  $type
     * @param  mixed  $value
     * @return mixed
     *
     * @throws \Exception
     */
    public static function convertToType(string $type, $value)
    {
        if ($value === null) {
            return null;
        }

        if ($type === 'bool' || $type === 'boolean') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        $succeeded = settype($value, $type);

        if ($succeeded) {
            return $value;
        }

        throw new \Exception(trans('laravel-settings::general.exceptions.conversion_failed', compact('type')));
    }

    /**
     * Retrieves the configured settings with all the required keys.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getConfigured(): Collection
    {
        $minimum = [
            'title' => null,
            'description' => null,
            'type' => 'string',
            'default' => null,
        ];

        return static::getRawConfigured()->map(function ($config) use ($minimum) {
            return $config + $minimum;
        });
    }

    /**
     * Retrieves the raw configured settings.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getRawConfigured(): Collection
    {
        return collect(config('laravel-settings.settings', []));
    }
}
