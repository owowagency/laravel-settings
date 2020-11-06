<?php

namespace OwowAgency\LaravelSettings\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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
                [
                    'value' => $setting['value'],
                    'group' => $setting['group'] ?? null,
                ],
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
        return new SettingCollection(
            static::mapSettingsConfig(static::getConfigured(), $settings)
        );
    }

    /**
     * Map each setting config so that it contains the correct settings.
     *
     * @param  \Illuminate\Support\Collection|array  $config
     * @param  \Illuminate\Support\Collection  $settings
     * @param  \Illuminate\Support\Collection|null  $groups
     * @return \Illuminate\Support\Collection
     */
    protected static function mapSettingsConfig(
        $config,
        Collection $settings,
        ?Collection $groups = null
    ): Collection {
        return collect($config)->map(function ($config, $key) use ($settings, $groups) {
            return static::addSettingsToConfig(
                $config,
                $key,
                $settings,
                $groups ?? new Collection()
            );
        });
    }

    /**
     * Add the given settings to the config.
     *
     * @param  array  $config
     * @param  string  $key
     * @param  \Illuminate\Support\Collection  $settings
     * @param  \Illuminate\Support\Collection  $groups
     * @return \Illuminate\Support\Collection|array
     */
    protected static function addSettingsToConfig(
        array $config,
        string $key,
        Collection $settings,
        Collection $groups
    ) {
        // If the config is a group config we need to map each config of that
        // group with the correct settings.
        if (self::isGroup($config)) {
            $groups->add($key);

            return static::mapSettingsConfig($config, $settings, $groups);
        }

        $setting = $settings->where('key', $key)
            ->where('group', count($groups) > 0 ? $groups->implode('.') : null)
            ->first();

        $config['key'] = $key;
        $config['value'] = $setting === null
            ? $config['default']
            : $setting->value;

        return $config;
    }

    /**
     * Fill the given setting models with the minimal configuration values.
     *
     * @param  array  $settings
     * @return \Illuminate\Support\Collection
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
        if (Str::contains($key, '.')) {
            return static::groupExists($key);
        }

        return static::getConfigured()->offsetExists($key);
    }

    /**
     * Determine if a key exists in the setting configuration.
     *
     * @param  string  $key
     * @return bool
     */
    public static function groupExists(string $key): bool
    {
        return is_array(data_get(self::getRawConfigured(), $key));
    }

    /**
     * Determine if the given configuration array is a setting group.
     *
     * @param  array|\Illuminate\Support\Collection  $config
     * @return bool
     */
    public static function isGroup($config): bool
    {
        $config = is_array($config) ? $config : $config->toArray();

        return count($config) !== count($config, COUNT_RECURSIVE);
    }

    /**
     * Retrieves the configured settings with all the required keys.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getConfigured(): Collection
    {
        return static::mapConfigurationWithMinimum(static::getRawConfigured());
    }

    /**
     * Map over each configuration and add the minimum keys.
     *
     * @param  mixed  $config
     * @return \Illuminate\Support\Collection
     */
    protected static function mapConfigurationWithMinimum($config): Collection
    {
        return collect($config)->map(function ($config) {
            return static::addMinimumKeys($config);
        });
    }

    /**
     * Add the minimal required keys for settings.
     *
     * @param  array  $config
     * @return array
     */
    protected static function addMinimumKeys(array $config): array
    {
        // If the configuration is a group we don't want to add the keys to it.
        // Instead, we'll just map the configuration group and add the minimum
        // keys to it.
        if (static::isGroup($config)) {
            return static::mapConfigurationWithMinimum($config)->toArray();
        }

        return $config + static::getMinimumConfig();
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
