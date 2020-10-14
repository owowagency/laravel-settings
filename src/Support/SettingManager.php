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

        return static::fillMissing($settings);
    }

    /**
     * Fills the missing settings.
     *
     * @param  \Illuminate\Support\Collection  $settings
     * @return \Illuminate\Support\Collection
     */
    public static function fillMissing(
        Collection $settings
    ): Collection {
        $configured = config('laravel-settings.settings', []);

        foreach ($configured as $key => $configuration) {
            $setting = $settings->where('key', $key)
                ->first();

            data_set($configured, "$key.key", $key);

            $value = $setting === null
                ? $configuration['default']
                : $setting->value;

            data_set($configured, "$key.value", $value);
        }

        return collect(array_values($configured));
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
        $raw = static::getRawConfigured();

        $minimum = [
            'title' => null,
            'description' => null,
            'type' => 'string',
            'default' => null,
        ];

        foreach ($raw as $key => $config) {
            $raw[$key] = $config + $minimum;
        }

        return collect($raw);
    }

    /**
     * Retrieves the raw configured settings.
     *
     * @return array
     */
    public static function getRawConfigured(): array
    {
        return config('laravel-settings.settings', []);
    }
}
