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
     * Create a new setting model and fill it with the default data.
     *
     * @param  \OwowAgency\LaravelSettings\Models\Contracts\HasSettingsInterface  $model
     * @param  string  $key
     * @return \OwowAgency\LaravelSettings\Models\Setting
     */
    public static function fillSettingModel(
        HasSettingsInterface $model,
        string $key
    ): Setting {
        $configuration = static::getConfiguration($key);

        return (new Setting())->forceFill([
            'model_id' => $model->id,
            'model_type' => $model->getMorphClass(),
            'key' => $key,
            'value' => data_get($configuration, 'default'),
        ]);
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
     * Retrieves the configured settings.
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function getConfigured(): Collection
    {
        return collect(config('laravel-settings.settings', []));
    }

    /**
     * Get the configuration values of the given setting by its key.
     *
     * @param  string  $key
     * @return array|null
     */
    public static function getConfiguration(string $key): ?array
    {
        return data_get(config('laravel-settings.settings', []), $key);
    }
}
