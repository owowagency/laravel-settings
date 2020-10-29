<?php

namespace OwowAgency\LaravelSettings\Models\Concerns;

use Illuminate\Support\Collection;
use OwowAgency\LaravelSettings\Models\Setting;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use OwowAgency\LaravelSettings\Support\SettingManager;

trait HasSettings
{
    /**
     * Get all of the model's settings.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function settings(): MorphMany
    {
        return $this->morphMany(Setting::class, 'model');
    }

    /**
     * Get the all the setting values for the current model.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSettings(): Collection
    {
        return SettingManager::getForModel($this);
    }

    /**
     * Get the settings configuration by the given key.
     *
     * @param  string  $key
     * @return array
     */
    public function getSettingConfig(string $key): array
    {
        return $this->getSettings()->firstWhere('key', $key);
    }

    /**
     * Get the settings value by the given key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getSettingValue(string $key)
    {
        $config = $this->getSettingConfig($key);

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
    public function getRawSettingValue(string $key)
    {
        return data_get($this->getSettingConfig($key), 'value');
    }
}
