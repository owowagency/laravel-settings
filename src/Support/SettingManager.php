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
     * @param  \OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface  $model
     * @return \OwowAgency\LaravelSettings\Support\SettingCollection
     */
    public static function getForModel(HasSettingsInterface $model): SettingCollection
    {
        $settings = $model->settings()->get();

        return static::mergeWithSettingsConfig($settings);
    }

    /**
     * Update the settings for the specified model and return all new values.
     *
     * @param  \OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface  $model
     * @param  array  $settings
     * @return \Illuminate\Support\Collection
     */
    public static function updateForModel(HasSettingsInterface $model, array $settings): Collection
    {
        foreach ($settings as $setting) {
            $model->settings()->updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']],
            );
        }

        return static::getForModel($model);
    }

    /**
     * Merge the settings collection with the settings config..
     *
     * @param  \Illuminate\Support\Collection  $settings
     * @return \OwowAgency\LaravelSettings\Support\SettingCollection
     */
    public static function mergeWithSettingsConfig(
        Collection $settings
    ): SettingCollection {
        $callback = function ($configuration, $key) use ($settings) {
            $setting = $settings->firstWhere('key', $key);

            $configuration['key'] = $key;

            // The settings config default values will be used as fallback.
            $configuration['value'] = $setting === null
                ? $configuration['default']
                : $setting->value;

            return $configuration;
        };

        return new SettingCollection(
            static::getConfigured()->map($callback)->values(),
        );
    }

    /**
     * Fill the given setting models with the minimal configuration values.
     *
     * @param  array  $settings
     * @return Collection
     */
    public static function fillWithSettingsConfig(array $settings): Collection
    {
        $configured = static::getConfigured();

        return collect($settings)->map(function (Setting $setting) use ($configured) {
            return $setting->forceFill(
                $configured[$setting->key] ?? static::getMinimumConfig(),
            );
        });
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
     * Determine if a key exists in the setting configuration.
     *
     * @param  string  $key
     * @return bool
     */
    public static function exists(string $key): bool
    {
        return static::getConfigured()->offsetExists($key);
    }

    /**
     * Retrieves the configured settings with all the required keys.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getConfigured(): Collection
    {
        $minimum = static::getMinimumConfig();

        return static::getRawConfigured()->map(function ($config) use ($minimum) {
            return $config + $minimum;
        });
    }

    /**
     * Get the minimum configuration.
     *
     * @return array
     */
    public static function getMinimumConfig(): array
    {
        return [
            'title' => null,
            'description' => null,
            'type' => 'string',
            'default' => null,
            'nullable' => true,
        ];
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
