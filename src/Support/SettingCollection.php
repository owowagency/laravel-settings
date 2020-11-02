<?php

namespace OwowAgency\LaravelSettings\Support;

use Illuminate\Database\Eloquent\Collection;

class SettingCollection extends Collection
{
    /**
     * Get the settings configuration by the given key.
     *
     * @param  string  $key
     * @return array|null
     */
    public function getConfig(string $key): ?array
    {
        return $this->firstWhere('key', $key);
    }

    /**
     * Get the settings value by the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getValue(string $key)
    {
        $config = $this->getConfig($key);

        return SettingManager::convertToType(
            data_get($config, 'type'),
            data_get($config, 'value'),
        );
    }

    /**
     * Get the unconverted settings value by the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getRawValue(string $key)
    {
        return data_get($this->getConfig($key), 'value');
    }

    /**
     * Dynamically access collection proxies.
     *
     * @param  string  $key
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get($key)
    {
        if (array_key_exists($key, config('laravel-settings.settings'))) {
            return $this->getValue($key);
        }

        return parent::__get($key);
    }
}
